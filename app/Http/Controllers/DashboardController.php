<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Department;
use App\Models\Kpi;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
