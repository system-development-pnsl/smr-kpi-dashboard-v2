{{-- resources/views/pages/tasks/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Tasks')
@section('page-title', 'Task Management')
@section('page-sub', 'All departmental tasks & workflows')

@section('content')
<div class="space-y-4">

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Search --}}
        <form method="GET" action="{{ route('tasks.index') }}"
              class="flex items-center gap-2 bg-brand-surface border border-brand-border rounded-lg px-3 h-9 flex-1 min-w-[180px] max-w-[280px]">
            <svg class="w-[13px] h-[13px] text-brand-subtle flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search tasks…"
                   class="bg-transparent text-[12px] text-brand-black placeholder:text-brand-subtle outline-none w-full">
        </form>

        {{-- Filters --}}
        <form method="GET" action="{{ route('tasks.index') }}" id="filter-form" class="contents">
            @foreach(request()->except(['department', 'priority', 'status']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach

            <x-select-search
                name="department"
                :options="$departments->map(fn($d) => ['value' => $d->code, 'label' => $d->label])->all()"
                placeholder="All Departments"
                :selected="request('department')"
                form-id="filter-form"
                class="w-44"
            />

            <x-select-search
                name="priority"
                :options="[['value'=>'P1','label'=>'Urgent'],['value'=>'P2','label'=>'High'],['value'=>'P3','label'=>'Normal'],['value'=>'P4','label'=>'Low']]"
                placeholder="All Priorities"
                :selected="request('priority')"
                form-id="filter-form"
                class="w-36"
            />

            <x-select-search
                name="status"
                :options="[['value'=>'TODO','label'=>'To Do'],['value'=>'IN_PROGRESS','label'=>'In Progress'],['value'=>'BLOCKED','label'=>'Blocked'],['value'=>'DONE','label'=>'Done']]"
                placeholder="All Statuses"
                :selected="request('status')"
                form-id="filter-form"
                class="w-36"
            />
        </form>

        <div class="flex-1"></div>

        {{-- View toggle --}}
        <div class="flex items-center gap-1 bg-brand-bg border border-brand-border rounded-lg p-0.5 h-9">
            <button id="btn-view-list"
                    class="h-7 px-2.5 rounded transition-all flex items-center gap-1 bg-brand-surface shadow-card text-brand-black">
                <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <span class="text-[11px] font-medium hidden sm:inline">List</span>
            </button>
            <button id="btn-view-kanban"
                    class="h-7 px-2.5 rounded transition-all flex items-center gap-1 text-brand-muted">
                <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                <span class="text-[11px] font-medium hidden sm:inline">Kanban</span>
            </button>
        </div>

        <a href="{{ route('tasks.create') }}" class="btn-primary">
            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Task
        </a>
    </div>

    {{-- ── List View ──────────────────────────────────────────── --}}
    <div id="task-view-list">
        <div class="card overflow-hidden p-0">
            {{-- Table header --}}
            <div class="grid grid-cols-12 gap-3 table-header">
                <div class="col-span-5">Task</div>
                <div class="col-span-2 hidden sm:block">Priority</div>
                <div class="col-span-2 hidden md:block">Assignee</div>
                <div class="col-span-2 hidden md:block">Due Date</div>
                <div class="col-span-1">Actions</div>
            </div>

            @forelse($tasks as $task)
                <div class="data-row group grid grid-cols-12 gap-3">
                    {{-- Task info --}}
                    <div class="col-span-5 flex items-center gap-2.5 min-w-0">
                        <div class="w-2 h-2 rounded-full flex-shrink-0
                            {{ ['TODO' => 'bg-brand-muted', 'IN_PROGRESS' => 'bg-status-blue', 'BLOCKED' => 'bg-status-red', 'DONE' => 'bg-status-green'][$task->status] ?? 'bg-brand-muted' }}">
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('tasks.show', $task) }}" class="text-[12px] font-medium text-brand-black hover:underline truncate block">
                                {{ $task->title }}
                            </a>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white uppercase"
                                      style="background-color: {{ $task->department->color ?? '#737373' }}">
                                    {{ $task->department->code ?? '' }}
                                </span>
                                <span class="text-[11px] text-brand-muted truncate">{{ $task->category }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Priority --}}
                    <div class="col-span-2 hidden sm:flex items-center">
                        <span class="priority-{{ $task->priority }}">
                            {{ ['P1' => 'Urgent', 'P2' => 'High', 'P3' => 'Normal', 'P4' => 'Low'][$task->priority] }}
                        </span>
                    </div>

                    {{-- Assignees --}}
                    <div class="col-span-2 hidden md:flex items-center">
                        <div class="flex -space-x-1.5">
                            @foreach($task->assignees->take(3) as $assignee)
                                <div class="w-5 h-5 rounded-full bg-brand-bg border border-brand-surface flex items-center justify-center text-[8px] font-semibold text-brand-muted"
                                     title="{{ $assignee->full_name }}">
                                    {{ strtoupper(substr($assignee->full_name, 0, 1)) }}
                                </div>
                            @endforeach
                            @if($task->assignees->count() > 3)
                                <div class="w-5 h-5 rounded-full bg-brand-black border border-brand-surface flex items-center justify-center text-[8px] font-semibold text-white">
                                    +{{ $task->assignees->count() - 3 }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Due date --}}
                    <div class="col-span-2 hidden md:flex items-center">
                        <span class="text-[11px] {{ $task->is_overdue ? 'text-status-red font-medium' : 'text-brand-muted' }}">
                            {{ $task->due_date->format('d M Y') }}
                            @if($task->is_overdue)
                                <span class="text-[9px]">(overdue)</span>
                            @endif
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="col-span-1 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('tasks.edit', $task) }}" class="p-1 text-brand-muted hover:text-brand-black transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" data-remove-closest=".data-row">
                            @csrf @method('DELETE')
                            <button type="submit" data-confirm="This task will be permanently deleted." class="p-1 text-brand-muted hover:text-status-red transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-[12px] text-brand-muted">
                    No tasks match your filters.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        {{ $tasks->withQueryString()->links('components.pagination') }}
    </div>

    {{-- ── Kanban View ────────────────────────────────────────── --}}
    <div id="task-view-kanban" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3" style="display:none">
        @foreach(['TODO' => ['To Do', 'border-t-brand-muted'], 'IN_PROGRESS' => ['In Progress', 'border-t-status-blue'], 'BLOCKED' => ['Blocked', 'border-t-status-red'], 'DONE' => ['Done', 'border-t-status-green']] as $status => [$label, $borderClass])
            <div class="bg-brand-surface border border-brand-border rounded-xl overflow-hidden border-t-2 {{ $borderClass }}">
                <div class="flex items-center justify-between px-3 py-2.5 border-b border-brand-border">
                    <div class="flex items-center gap-2">
                        <div class="dot {{ ['TODO'=>'dot-gray','IN_PROGRESS'=>'dot-blue','BLOCKED'=>'dot-red','DONE'=>'dot-green'][$status] }}"></div>
                        <span class="text-[11px] font-semibold text-brand-black">{{ $label }}</span>
                    </div>
                    <span class="text-[10px] font-medium text-brand-muted bg-brand-bg px-1.5 py-0.5 rounded">
                        {{ $kanbanTasks[$status]->count() }}
                    </span>
                </div>
                <div class="p-2 space-y-2 min-h-[120px]">
                    @forelse($kanbanTasks[$status] as $task)
                        <a href="{{ route('tasks.show', $task) }}"
                           class="block bg-brand-bg border border-brand-border rounded-lg p-3
                                  hover:border-brand-black/20 hover:shadow-card transition-all
                                  {{ $task->is_overdue ? 'border-status-red/30 bg-status-red-bg/20' : '' }}">
                            <p class="text-[12px] font-medium text-brand-black leading-snug mb-1.5">{{ $task->title }}</p>
                            <p class="text-[10px] text-brand-muted mb-2">{{ $task->category }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white uppercase"
                                      style="background-color: {{ $task->department->color ?? '#737373' }}">
                                    {{ $task->department->code ?? '' }}
                                </span>
                                <span class="priority-{{ $task->priority }}">
                                    {{ ['P1'=>'Urgent','P2'=>'High','P3'=>'Normal','P4'=>'Low'][$task->priority] }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-brand-border/60">
                                <span class="text-[10px] text-brand-muted truncate">
                                    {{ $task->assignees->pluck('full_name')->first() ?? '—' }}
                                </span>
                                <span class="text-[10px] {{ $task->is_overdue ? 'text-status-red font-medium' : 'text-brand-subtle' }}">
                                    {{ $task->due_date->format('d M') }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <p class="text-center py-6 text-[11px] text-brand-subtle">
                            {{ $status === 'DONE' ? '🎉 All done!' : 'No tasks' }}
                        </p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
