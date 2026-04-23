<?php

namespace App\Http\Controllers;

use App\Models\{Task, Kpi, BankAccount, Department, User, ActionPlan, CashFlowEntry};
use App\Models\{Document, MonthlyReport};
use App\Services\AiDocumentService;
use App\Jobs\ProcessDocumentWithAI;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\{Auth, Hash, Storage, Log};
use Illuminate\View\View;

// =============================================================================
// AuthController
// =============================================================================
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('pages.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])
            ->where('status', 'active')->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'The provided credentials are incorrect.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showForgot(): View   { return view('pages.auth.forgot'); }
    public function showReset(): View    { return view('pages.auth.reset'); }
    public function sendReset(): RedirectResponse  { return back()->with('status', 'Reset link sent.'); }
    public function resetPassword(): RedirectResponse { return redirect()->route('login')->with('success', 'Password reset.'); }
}

// =============================================================================
// DashboardController
// =============================================================================
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->get('status', 'all');

        $taskQuery = Task::with(['assignees:id,full_name', 'department:id,code,label,color'])
            ->latest('updated_at')->limit(10);

        if ($statusFilter !== 'all') {
            $taskQuery->where('status', $statusFilter);
        }

        $hotelWideKpis = Kpi::with(['department:id,code,label', 'latestEntry'])
            ->where('is_hotel_wide', true)->where('is_active', true)->get()
            ->each(fn($kpi) => $kpi->history = $kpi->entries()->latest('period')->limit(12)->pluck('value')->reverse()->values());

        $tasksByDept = Department::withCount([
            'tasks as total_count',
            'tasks as done_count'  => fn($q) => $q->where('status', 'DONE'),
            'tasks as overdue_count'=> fn($q) => $q->where('status', '!=', 'DONE')->where('due_date', '<', now()),
        ])->where('is_active', true)->orderBy('sort_order')->get();

        $bankAccounts = BankAccount::with('latestBalance')->where('is_active', true)->orderBy('sort_order')->get();
        $totalCash    = $bankAccounts->sum(fn($a) => $a->latestBalance?->closing_balance ?? 0);

        $revenueLabels   = ['May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar','Apr'];
        $revenueDatasets = [
            ['label' => 'Rooms', 'data' => [185000,192000,198000,195000,205000,218000,225000,221000,230000,226000,238000,220000]],
            ['label' => 'F&B',   'data' => [42000,45000,47000,46000,49000,52000,54000,53000,56000,55000,58000,48000]],
            ['label' => 'Spa',   'data' => [18000,19500,20000,19000,21000,22500,23000,22000,24000,23500,25000,21000]],
            ['label' => 'Other', 'data' => [8000,8500,9000,8800,9200,9800,10000,9500,10500,10200,11000,9200]],
        ];

        return view('pages.dashboard.index', [
            'kpis'          => $hotelWideKpis,
            'recentTasks'   => $taskQuery->get(),
            'tasksByDept'   => $tasksByDept,
            'bankAccounts'  => $bankAccounts,
            'notifications' => auth()->user()->notifications()->latest()->limit(5)->get(),
            'unreadCount'   => auth()->user()->unreadNotifications->count(),
            'stats'         => [
                'total_cash'    => $totalCash,
                'open_tasks'    => Task::whereNotIn('status', ['DONE', 'CANCELLED'])->count(),
                'overdue_tasks' => Task::where('due_date', '<', now())->whereNotIn('status', ['DONE', 'CANCELLED'])->count(),
                'unread_alerts' => auth()->user()->unreadNotifications->count(),
            ],
            'revenueChart'  => ['labels' => $revenueLabels, 'datasets' => $revenueDatasets],
        ]);
    }

    public function search(Request $request): View
    {
        $q     = $request->get('q', '');
        $tasks = Task::where('title', 'like', "%{$q}%")->limit(10)->get();
        $kpis  = Kpi::where('name', 'like', "%{$q}%")->limit(5)->get();
        return view('pages.dashboard.search', compact('q', 'tasks', 'kpis'));
    }
}

// =============================================================================
// TaskController
// =============================================================================
class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $user  = auth()->user();
        $query = Task::with(['assignees:id,full_name', 'department:id,code,label,color'])->latest();

        match ($user->role) {
            'staff'        => $query->whereHas('assignees', fn($q) => $q->where('users.id', $user->id)),
            'head_of_dept' => $query->where('department_id', $user->department_id),
            default        => null,
        };

        $query
            ->when($request->department, fn($q) => $q->whereHas('department', fn($d) => $d->where('code', $request->department)))
            ->when($request->priority,   fn($q) => $q->where('priority', $request->priority))
            ->when($request->status,     fn($q) => $q->where('status', $request->status))
            ->when($request->search,     fn($q) => $q->where('title', 'like', "%{$request->search}%"));

        $kanbanTasks = [];
        foreach (['TODO', 'IN_PROGRESS', 'BLOCKED', 'DONE'] as $status) {
            $kanbanTasks[$status] = $query->clone()->where('status', $status)->limit(20)->get();
        }

        return view('pages.tasks.index', [
            'tasks'       => $query->paginate(20),
            'kanbanTasks' => $kanbanTasks,
            'departments' => Department::orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.tasks.form', [
            'departments' => Department::orderBy('sort_order')->get(),
            'users'       => User::with('department')->where('status', 'active')->orderBy('full_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:200',
            'description'   => 'nullable|string|max:1000',
            'priority'      => 'required|in:P1,P2,P3,P4',
            'status'        => 'required|in:TODO,IN_PROGRESS,BLOCKED,DONE,CANCELLED',
            'due_date'      => 'required|date',
            'start_date'    => 'nullable|date',
            'department_id' => 'required|exists:departments,id',
            'assignee_ids'  => 'required|array|min:1',
            'category'      => 'nullable|string|max:100',
            'tags_input'    => 'nullable|string',
        ]);

        $dept  = Department::find($validated['department_id']);
        $count = Task::where('department_id', $validated['department_id'])->withTrashed()->count() + 1;
        $tags  = array_filter(array_map('trim', explode(',', $validated['tags_input'] ?? '')));

        $task = Task::create([
            ...$validated,
            'task_number' => $dept->code . '-' . str_pad($count, 6, '0', STR_PAD_LEFT),
            'created_by'  => auth()->id(),
            'tags'        => $tags,
        ]);

        $task->assignees()->sync($validated['assignee_ids']);

        return redirect()->route('tasks.show', $task)->with('success', 'Task created successfully.');
    }

    public function show(Task $task): View
    {
        $task->load(['assignees:id,full_name,code', 'department', 'creator:id,full_name',
                     'subtasks.assignees:id,full_name', 'comments.user:id,full_name', 'auditLogs.user:id,full_name']);

        return view('pages.tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        return view('pages.tasks.form', [
            'task'        => $task->load('assignees'),
            'departments' => Department::orderBy('sort_order')->get(),
            'users'       => User::with('department')->where('status', 'active')->orderBy('full_name')->get(),
        ]);
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:200',
            'description'   => 'nullable|string|max:1000',
            'priority'      => 'required|in:P1,P2,P3,P4',
            'status'        => 'required|in:TODO,IN_PROGRESS,BLOCKED,DONE,CANCELLED',
            'due_date'      => 'required|date',
            'start_date'    => 'nullable|date',
            'department_id' => 'required|exists:departments,id',
            'assignee_ids'  => 'required|array|min:1',
            'category'      => 'nullable|string|max:100',
            'tags_input'    => 'nullable|string',
        ]);

        $tags = array_filter(array_map('trim', explode(',', $validated['tags_input'] ?? '')));
        $task->update([...$validated, 'tags' => $tags]);
        $task->assignees()->sync($validated['assignee_ids']);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $request->validate(['status' => 'required|in:TODO,IN_PROGRESS,BLOCKED,DONE,CANCELLED']);
        $task->update([
            'status'       => $request->status,
            'completed_at' => $request->status === 'DONE' ? now() : null,
        ]);
        return back()->with('success', 'Status updated.');
    }

    public function addComment(Request $request, Task $task): RedirectResponse
    {
        $request->validate(['body' => 'required|string|max:2000']);
        $task->comments()->create(['user_id' => auth()->id(), 'body' => $request->body]);
        return back()->with('success', 'Comment added.');
    }

    public function addSubtask(Request $request, Task $task): RedirectResponse
    {
        $request->validate(['title' => 'required|string|max:200', 'due_date' => 'required|date']);
        $count = Task::whereNotNull('parent_task_id')->count() + 1;
        $sub   = Task::create([
            'title'          => $request->title,
            'due_date'       => $request->due_date,
            'priority'       => $request->priority ?? 'P3',
            'status'         => 'TODO',
            'parent_task_id' => $task->id,
            'department_id'  => $task->department_id,
            'created_by'     => auth()->id(),
            'task_number'    => 'SUB-' . str_pad($count, 6, '0', STR_PAD_LEFT),
        ]);
        if ($request->filled('assignee_id')) {
            $sub->assignees()->attach($request->assignee_id);
        }
        return back()->with('success', 'Sub-task added.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $request->validate(['task_ids' => 'required|array', 'action' => 'required|string']);
        match ($request->action) {
            'delete'      => Task::whereIn('id', $request->task_ids)->delete(),
            'mark_done'   => Task::whereIn('id', $request->task_ids)->update(['status' => 'DONE']),
            'mark_todo'   => Task::whereIn('id', $request->task_ids)->update(['status' => 'TODO']),
            default       => null,
        };
        return back()->with('success', 'Bulk action applied.');
    }
}

// =============================================================================
// KpiController
// =============================================================================
class KpiController extends Controller
{
    public function index(Request $request): View
    {
        $user  = auth()->user();
        $query = Kpi::with(['department:id,code,label,color', 'latestEntry'])->where('is_active', true);

        if ($user->role === 'head_of_dept') {
            $query->where('department_id', $user->department_id);
        }

        $kpis = $query->get()->each(fn($k) => $k->history = $k->entries()->latest('period')->limit(12)->pluck('value')->reverse()->values());

        // Filter by status
        if ($request->filled('status') && $request->status !== '') {
            $kpis = $kpis->filter(fn($k) => match($request->status) {
                'on_track'    => $k->currentStatus() === 'green',
                'near_target' => $k->currentStatus() === 'amber',
                'off_track'   => $k->currentStatus() === 'red',
                default       => true,
            });
        }

        $summary = [
            'on_track'  => $query->get()->filter(fn($k) => $k->currentStatus() === 'green')->count(),
            'at_risk'   => $query->get()->filter(fn($k) => $k->currentStatus() === 'amber')->count(),
            'off_track' => $query->get()->filter(fn($k) => $k->currentStatus() === 'red')->count(),
        ];

        $actionPlans = ActionPlan::with(['department', 'goals.actionItems'])->where('year', now()->year)
            ->where('quarter', ceil(now()->month / 3))->get();

        return view('pages.kpi.index', compact('kpis', 'summary', 'actionPlans'));
    }

    public function show(Kpi $kpi): View
    {
        $kpi->load(['department', 'entries' => fn($q) => $q->latest('period')->limit(24)]);
        return view('pages.kpi.show', compact('kpi'));
    }

    public function addEntry(Request $request, Kpi $kpi): RedirectResponse
    {
        $request->validate(['value' => 'required|numeric', 'period' => 'required|date', 'note' => 'nullable|string|max:500']);
        $kpi->entries()->create(['value' => $request->value, 'period' => $request->period, 'note' => $request->note, 'source' => 'manual', 'submitted_by' => auth()->id()]);
        return back()->with('success', 'KPI value submitted.');
    }

    public function actionPlan(ActionPlan $plan): View
    {
        $plan->load(['department', 'goals.actionItems.assignee', 'creator']);
        return view('pages.kpi.action-plan', compact('plan'));
    }

    public function addGoal(Request $request, ActionPlan $plan): RedirectResponse
    {
        $request->validate(['title' => 'required|string|max:200', 'target_date' => 'required|date', 'owner_id' => 'required|exists:users,id']);
        $plan->goals()->create([...$request->only('title', 'description', 'target_date', 'owner_id'), 'status' => 'on_track', 'sort_order' => $plan->goals()->count()]);
        return back()->with('success', 'Goal added.');
    }
}

// =============================================================================
// FinancialController
// =============================================================================
class FinancialController extends Controller
{
    public function index(): View
    {
        $this->authorizeFinancialAccess();

        $period      = now()->format('Y-m');
        $bankAccounts= BankAccount::with('latestBalance')->where('is_active', true)->orderBy('sort_order')->get();
        $totalCash   = $bankAccounts->sum(fn($a) => $a->latestBalance?->closing_balance ?? 0);

        $entries  = CashFlowEntry::where('period', 'like', "{$period}%")->get();
        $inflows  = $entries->where('type', 'INFLOW');
        $outflows = $entries->where('type', 'OUTFLOW');

        $cashFlow = [
            'inflows'       => $inflows,
            'outflows'      => $outflows,
            'total_inflow'  => $inflows->sum('amount'),
            'total_outflow' => $outflows->sum('amount'),
            'net_position'  => $inflows->sum('amount') - $outflows->sum('amount'),
        ];

        $trendLabels   = ['May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar','Apr'];
        $trendDatasets = [
            ['label' => 'ABA Bank', 'data' => collect(range(0,11))->map(fn($i) => 110000 + $i * 1800 + sin($i) * 4000)->all()],
            ['label' => 'ACLEDA',   'data' => collect(range(0,11))->map(fn($i) => 75000  + $i * 900  + cos($i) * 3000)->all()],
        ];

        return view('pages.financial.index', [
            'bankAccounts' => $bankAccounts,
            'totalCash'    => $totalCash,
            'cashFlow'     => $cashFlow,
            'trendChart'   => ['labels' => $trendLabels, 'datasets' => $trendDatasets],
        ]);
    }

    public function transactions(): View
    {
        $this->authorizeFinancialAccess();
        $transactions = \App\Models\Transaction::with('recordedBy:id,full_name')->latest('transaction_date')->paginate(25);
        return view('pages.financial.transactions', compact('transactions'));
    }

    public function storeCashFlow(Request $request): RedirectResponse
    {
        $this->authorizeFinancialAccess();
        return back()->with('success', 'Cash flow submitted for approval.');
    }

    public function updateBalance(Request $request, BankAccount $account): RedirectResponse
    {
        $this->authorizeFinancialAccess();
        $request->validate(['closing_balance' => 'required|numeric', 'balance_date' => 'required|date']);
        $account->balances()->create([
            'balance_date'    => $request->balance_date,
            'opening_balance' => $account->latestBalance?->closing_balance ?? 0,
            'closing_balance' => $request->closing_balance,
            'source'          => 'manual',
            'recorded_by'     => auth()->id(),
        ]);
        return back()->with('success', 'Balance updated.');
    }

    private function authorizeFinancialAccess(): void
    {
        abort_unless(auth()->user()->hasFinancialAccess(), 403, 'You do not have access to the Financial Dashboard.');
    }
}

// =============================================================================
// DocumentController
// =============================================================================
class DocumentController extends Controller
{
    public function __construct(private readonly AiDocumentService $aiService) {}

    public function index(): View
    {
        $user  = auth()->user();
        $query = Document::with(['uploadedBy:id,full_name', 'department:id,code,label'])->latest();
        if ($user->role === 'head_of_dept') {
            $query->where('department_id', $user->department_id);
        }
        return view('pages.documents.index', ['documents' => $query->paginate(20), 'departments' => Department::orderBy('sort_order')->get()]);
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file'          => 'required|file|max:25600|mimes:pdf,docx,doc,xlsx,xls,csv,png,jpg,jpeg',
            'department_id' => 'required|exists:departments,id',
            'description'   => 'nullable|string|max:500',
        ]);

        $file     = $request->file('file');
        $filename = \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('documents/' . now()->format('Y/m'), $filename, 'private');

        $doc = Document::create([
            'original_name' => $file->getClientOriginalName(),
            'stored_path'   => $path,
            'mime_type'     => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
            'file_type'     => strtoupper($file->getClientOriginalExtension()),
            'sha256'        => hash_file('sha256', $file->getRealPath()),
            'department_id' => $request->department_id,
            'description'   => $request->description,
            'uploaded_by'   => auth()->id(),
            'ai_status'     => 'pending',
        ]);

        ProcessDocumentWithAI::dispatch($doc);

        return redirect()->route('documents.show', $doc)->with('success', 'Document uploaded. AI processing started.');
    }

    public function show(Document $document): View
    {
        return view('pages.documents.show', compact('document'));
    }

    public function confirm(Request $request, Document $document): RedirectResponse
    {
        $request->validate(['confirmed_fields' => 'required|array']);
        $document->update(['confirmed_fields' => $request->confirmed_fields, 'ai_status' => 'confirmed', 'confirmed_at' => now(), 'confirmed_by' => auth()->id()]);
        $this->aiService->pushToDashboard($document, $request->confirmed_fields);
        return redirect()->route('documents.index')->with('success', 'Data confirmed and pushed to dashboard.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        Storage::disk('private')->delete($document->stored_path);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }

    public function download(Document $document)
    {
        return Storage::disk('private')->download($document->stored_path, $document->original_name);
    }
}

// =============================================================================
// NotificationController
// =============================================================================
class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('pages.notifications.index', compact('notifications'));
    }

    public function markRead(string $id): RedirectResponse
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(string $id): RedirectResponse
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        return back();
    }
}

// =============================================================================
// ReportController
// =============================================================================
class ReportController extends Controller
{
    public function index(): View
    {
        $reports = MonthlyReport::with('department')->orderByDesc('period')->paginate(20);
        return view('pages.reports.index', compact('reports'));
    }

    public function show(MonthlyReport $report): View
    {
        return view('pages.reports.show', compact('report'));
    }

    public function download(MonthlyReport $report)
    {
        if (! $report->pdf_path || ! Storage::disk('private')->exists($report->pdf_path)) {
            return back()->with('error', 'PDF not yet generated.');
        }
        return Storage::disk('private')->download($report->pdf_path, "SMR_Report_{$report->period->format('Y-m')}.pdf");
    }
}

// =============================================================================
// UserController
// =============================================================================
class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('department')->orderBy('full_name')->paginate(25);
        return view('pages.users.index', ['users' => $users, 'departments' => Department::orderBy('sort_order')->get()]);
    }

    public function create(): View
    {
        return view('pages.users.form', ['departments' => Department::orderBy('sort_order')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'role'          => 'required|in:owner,general_manager,agm,finance_director,head_of_dept,staff',
            'department_id' => 'required|exists:departments,id',
            'job_title'     => 'required|string|max:100',
        ]);
        $dept  = Department::find($data['department_id']);
        $count = User::where('department_id', $data['department_id'])->count() + 1;
        User::create([...$data, 'code' => $dept->code.'-'.str_pad($count,3,'0',STR_PAD_LEFT), 'password' => Hash::make('Password@2026'), 'status' => 'active', 'access_modules' => ['tasks','kpi']]);
        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        return view('pages.users.form', ['user' => $user, 'departments' => Department::orderBy('sort_order')->get()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $user->update($request->validate(['full_name' => 'required|string|max:100', 'job_title' => 'required|string|max:100', 'role' => 'required', 'department_id' => 'required|exists:departments,id']));
        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->update(['status' => 'inactive']);
        return redirect()->route('users.index')->with('success', 'User deactivated.');
    }

    public function profile(): View
    {
        return view('pages.users.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        auth()->user()->update($request->validate(['full_name' => 'required|string|max:100', 'phone' => 'nullable|string|max:20']));
        return back()->with('success', 'Profile updated.');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate(['current_password' => 'required', 'password' => 'required|min:10|confirmed']);
        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password changed.');
    }
}
