<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $user  = Auth::user();
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

    public function store(Request $request): JsonResponse|RedirectResponse
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
            'created_by'  => Auth::id(),
            'tags'        => $tags,
        ]);

        $task->assignees()->sync($validated['assignee_ids']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task created successfully.', 'redirect' => route('tasks.show', $task)]);
        }
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

    public function update(Request $request, Task $task): JsonResponse|RedirectResponse
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

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task updated.', 'redirect' => route('tasks.show', $task)]);
        }
        return redirect()->route('tasks.show', $task)->with('success', 'Task updated.');
    }

    public function destroy(Task $task): JsonResponse|RedirectResponse
    {
        $task->delete();
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task deleted.']);
        }
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    public function updateStatus(Request $request, Task $task): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $request->validate(['status' => 'required|in:TODO,IN_PROGRESS,BLOCKED,DONE,CANCELLED']);
        $task->update([
            'status'       => $request->status,
            'completed_at' => $request->status === 'DONE' ? now() : null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $task->status]);
        }
        return back()->with('success', 'Status updated.');
    }

    public function addComment(Request $request, Task $task): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $request->validate(['body' => 'required|string|max:2000']);
        $comment = $task->comments()->create(['user_id' => Auth::id(), 'body' => $request->body]);
        $comment->load('user:id,full_name');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'body'         => $comment->body,
                    'user_name'    => $comment->user?->full_name ?? '—',
                    'user_initial' => strtoupper(substr($comment->user?->full_name ?? '?', 0, 1)),
                    'created_at'   => $comment->created_at->format('d M Y H:i'),
                ],
            ]);
        }
        return back()->with('success', 'Comment added.');
    }

    public function addSubtask(Request $request, Task $task): \Illuminate\Http\JsonResponse|RedirectResponse
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
            'created_by'     => Auth::id(),
            'task_number'    => 'SUB-' . str_pad($count, 6, '0', STR_PAD_LEFT),
        ]);
        if ($request->filled('assignee_id')) {
            $sub->assignees()->attach($request->assignee_id);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'subtask' => [
                    'title'       => $sub->title,
                    'task_number' => $sub->task_number,
                    'status'      => $sub->status,
                    'due_date'    => $sub->due_date->format('d M Y'),
                    'show_url'    => route('tasks.show', $sub),
                ],
            ]);
        }
        return back()->with('success', 'Sub-task added.');
    }

    public function bulk(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate(['task_ids' => 'required|array', 'action' => 'required|string']);
        match ($request->action) {
            'delete'    => Task::whereIn('id', $request->task_ids)->delete(),
            'mark_done' => Task::whereIn('id', $request->task_ids)->update(['status' => 'DONE']),
            'mark_todo' => Task::whereIn('id', $request->task_ids)->update(['status' => 'TODO']),
            default     => null,
        };
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Bulk action applied.']);
        }
        return back()->with('success', 'Bulk action applied.');
    }
}
