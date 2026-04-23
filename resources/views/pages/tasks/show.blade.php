@extends('layouts.app')
@section('title', $task->task_number . ' — ' . $task->title)
@section('page-title', 'Task Detail')
@section('page-sub', $task->task_number)

@section('content')
    @php
        $priorityConfig = [
            'P1' => ['Urgent', 'badge-red'],
            'P2' => ['High', 'badge-amber'],
            'P3' => ['Normal', 'badge-blue'],
            'P4' => ['Low', 'badge-gray'],
        ];
        $statusConfig = [
            'TODO' => ['To Do', 'dot-gray'],
            'IN_PROGRESS' => ['In Progress', 'dot-blue'],
            'BLOCKED' => ['Blocked', 'dot-red'],
            'DONE' => ['Done', 'dot-green'],
            'CANCELLED' => ['Cancelled', 'dot-gray'],
        ];
        [$prioLabel, $prioClass] = $priorityConfig[$task->priority] ?? ['Normal', 'badge-blue'];
        [$statusLabel, $statusDot] = $statusConfig[$task->status] ?? ['To Do', 'dot-gray'];
    @endphp

    <div class="space-y-4">

        {{-- Header card --}}
        <div class="card">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2 flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="{{ $statusDot }}"></div>
                        <h1 class="text-[15px] font-semibold text-brand-black leading-snug">{{ $task->title }}</h1>
                        @if ($task->is_overdue)
                            <span
                                class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-status-red-bg text-status-red uppercase">Overdue</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white uppercase"
                            style="background-color: {{ $task->department->color ?? '#737373' }}">
                            {{ $task->department->code ?? 'N/A' }}
                        </span>
                        <span class="{{ $prioClass }}">{{ $prioLabel }}</span>
                        @if ($task->category)
                            <span class="text-[11px] text-brand-muted">{{ $task->category }}</span>
                        @endif
                        <span class="text-[10px] font-mono text-brand-subtle">{{ $task->task_number }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary h-8 text-[11px]">Edit</a>
                    <a href="{{ route('tasks.index') }}" class="btn-secondary h-8 text-[11px]">← Back</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Main column --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Description --}}
                @if ($task->description)
                    <div class="card">
                        <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide mb-2">Description</p>
                        <p class="text-[12px] text-brand-black whitespace-pre-wrap leading-relaxed">
                            {{ $task->description }}</p>
                    </div>
                @endif

                {{-- Status update --}}
                <div class="card">
                    <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide mb-3">Update Status</p>
                    <form id="status-form" data-no-ajax method="POST" action="{{ route('tasks.update-status', $task) }}"
                        class="flex flex-wrap gap-2">
                        @csrf @method('PATCH')
                        @foreach ($statusConfig as $val => [$label, $dot])
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="{{ $val }}" class="sr-only"
                                    {{ $task->status === $val ? 'checked' : '' }} onchange="this.closest('form').submit()">
                                <div
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg border text-[11px] font-medium transition-all
                                        {{ $task->status === $val ? 'border-brand-black bg-brand-bg text-brand-black' : 'border-brand-border text-brand-muted hover:border-brand-black/30' }}">
                                    <div class="{{ $dot }}"></div>
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </form>
                </div>

                {{-- Sub-tasks --}}
                <div class="card overflow-hidden p-0">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-brand-border">
                        <p class="text-[12px] font-semibold text-brand-black">
                            Sub-tasks
                            <span id="subtask-count"
                                class="text-[11px] font-normal text-brand-muted ml-1">({{ $task->subtasks->count() }})</span>
                        </p>
                        <button id="btn-add-subtask" class="btn-secondary h-7 text-[11px]">+ Add</button>
                    </div>

                    {{-- Add subtask form --}}
                    <div id="subtask-form" class="px-4 py-3 border-b border-brand-border bg-brand-bg" style="display:none">
                        <form id="subtask-inner-form" method="POST" action="{{ route('tasks.subtask', $task) }}"
                            class="flex flex-wrap gap-2 items-end">
                            @csrf
                            <div class="flex flex-col gap-1 flex-1 min-w-[160px]">
                                <label
                                    class="text-[10px] text-brand-muted font-medium uppercase tracking-wide">Title</label>
                                <input type="text" name="title" required maxlength="200" class="input h-8 text-[12px]"
                                    placeholder="Sub-task title…">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[10px] text-brand-muted font-medium uppercase tracking-wide">Due
                                    Date</label>
                                <input type="date" name="due_date" required class="input h-8 text-[12px]">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="btn-primary h-8 text-[11px]">Add</button>
                                <button type="button" id="btn-cancel-subtask"
                                    class="btn-secondary h-8 text-[11px]">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <div id="subtasks-list">
                        @forelse($task->subtasks as $sub)
                            @php [$sl, $sd] = $statusConfig[$sub->status] ?? ['To Do', 'dot-gray']; @endphp
                            <div class="data-row flex items-center gap-3">
                                <div class="{{ $sd }}"></div>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('tasks.show', $sub) }}"
                                        class="text-[12px] font-medium text-brand-black hover:underline truncate block">
                                        {{ $sub->title }}
                                    </a>
                                    <span class="text-[10px] text-brand-subtle font-mono">{{ $sub->task_number }}</span>
                                </div>
                                <span
                                    class="text-[11px] text-brand-muted flex-shrink-0">{{ $sub->due_date->format('d M Y') }}</span>
                            </div>
                        @empty
                            <p id="subtasks-empty" class="text-center py-6 text-[11px] text-brand-subtle">No sub-tasks yet.
                            </p>
                        @endforelse
                    </div>
                </div>

                {{-- Comments --}}
                <div class="card overflow-hidden p-0">
                    <div class="px-4 py-3 border-b border-brand-border">
                        <p class="text-[12px] font-semibold text-brand-black">
                            Comments
                            <span id="comment-count"
                                class="text-[11px] font-normal text-brand-muted ml-1">({{ $task->comments->count() }})</span>
                        </p>
                    </div>

                    <div id="comments-list">
                        @forelse($task->comments->sortByDesc('created_at') as $comment)
                            <div class="px-4 py-3 border-b border-brand-border/60">
                                <div class="flex items-center gap-2 mb-1">
                                    <div
                                        class="w-5 h-5 rounded-full bg-brand-bg border border-brand-border flex items-center justify-center text-[8px] font-semibold text-brand-muted">
                                        {{ strtoupper(substr($comment->user?->full_name ?? '?', 0, 1)) }}
                                    </div>
                                    <span
                                        class="text-[11px] font-medium text-brand-black">{{ $comment->user?->full_name ?? '—' }}</span>
                                    <span
                                        class="text-[10px] text-brand-subtle">{{ $comment->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <p class="text-[12px] text-brand-black pl-7 whitespace-pre-wrap">{{ $comment->body }}</p>
                            </div>
                        @empty
                            <p id="comments-empty" class="text-center py-6 text-[11px] text-brand-subtle">No comments yet.
                            </p>
                        @endforelse
                    </div>

                    {{-- Add comment --}}
                    <div class="px-4 py-3 bg-brand-bg border-t border-brand-border">
                        <form id="comment-form" method="POST" action="{{ route('tasks.comment', $task) }}"
                            class="space-y-2">
                            @csrf
                            <textarea name="body" rows="2" required maxlength="2000" class="textarea text-[12px]"
                                placeholder="Write a comment…"></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary h-8 text-[11px]">Post Comment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar column --}}
            <div class="space-y-4">

                {{-- Task details --}}
                <div class="card space-y-3">
                    <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide">Details</p>

                    <div class="space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-[11px] text-brand-subtle">Status</span>
                            <div class="flex items-center gap-1.5">
                                <div class="{{ $statusDot }}"></div>
                                <span class="text-[11px] font-medium text-brand-black">{{ $statusLabel }}</span>
                            </div>
                        </div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-[11px] text-brand-subtle">Priority</span>
                            <span class="{{ $prioClass }}">{{ $prioLabel }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-[11px] text-brand-subtle">Department</span>
                            <span
                                class="text-[11px] font-medium text-brand-black">{{ $task->department->label ?? '—' }}</span>
                        </div>
                        @if ($task->start_date)
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-[11px] text-brand-subtle">Start Date</span>
                                <span class="text-[11px] text-brand-black">{{ $task->start_date->format('d M Y') }}</span>
                            </div>
                        @endif
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-[11px] text-brand-subtle">Due Date</span>
                            <span
                                class="text-[11px] font-medium {{ $task->is_overdue ? 'text-status-red' : 'text-brand-black' }}">
                                {{ $task->due_date->format('d M Y') }}
                            </span>
                        </div>
                        @if ($task->completed_at)
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-[11px] text-brand-subtle">Completed</span>
                                <span
                                    class="text-[11px] text-status-green">{{ $task->completed_at->format('d M Y') }}</span>
                            </div>
                        @endif
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-[11px] text-brand-subtle">Created by</span>
                            <span class="text-[11px] text-brand-black">{{ $task->creator?->full_name ?? '—' }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-[11px] text-brand-subtle">Created</span>
                            <span class="text-[11px] text-brand-muted">{{ $task->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Assignees --}}
                <div class="card space-y-2">
                    <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide">Assignees</p>
                    @forelse($task->assignees as $assignee)
                        <div class="flex items-center gap-2">
                            <div
                                class="w-6 h-6 rounded-full bg-brand-bg border border-brand-border flex items-center justify-center text-[9px] font-semibold text-brand-muted flex-shrink-0">
                                {{ strtoupper(substr($assignee->full_name, 0, 1)) }}
                            </div>
                            <span class="text-[12px] text-brand-black truncate">{{ $assignee->full_name }}</span>
                        </div>
                    @empty
                        <p class="text-[11px] text-brand-subtle">No assignees.</p>
                    @endforelse
                </div>
                {{-- Tags --}}
                @php $tagsArray = is_array($task->tags) ? $task->tags : (json_decode($task->tags, true) ?? []); @endphp
                @if (!empty($tagsArray))
                    <div class="card space-y-2">
                        <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide">Tags</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($tagsArray as $tag)
                                <span
                                    class="text-[10px] px-2 py-0.5 rounded bg-brand-bg border border-brand-border text-brand-muted">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Audit log --}}
                @if ($task->auditLogs->count())
                    <div class="card overflow-hidden p-0">
                        <div class="px-4 py-3 border-b border-brand-border">
                            <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide">Activity</p>
                        </div>
                        <div class="divide-y divide-brand-border/60 max-h-64 overflow-y-auto">
                            @foreach ($task->auditLogs->sortByDesc('created_at') as $log)
                                <div class="px-4 py-2.5">
                                    <p class="text-[11px] text-brand-black">
                                        {{ $log->action ?? ($log->event ?? 'Updated') }}
                                    </p>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <span
                                            class="text-[10px] text-brand-subtle">{{ $log->user?->full_name ?? 'System' }}</span>
                                        <span class="text-[10px] text-brand-subtle">·</span>
                                        <span
                                            class="text-[10px] text-brand-subtle">{{ $log->created_at->format('d M H:i') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function escHtml(s) {
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            // Subtask panel toggle
            document.getElementById('btn-add-subtask').addEventListener('click', function() {
                document.getElementById('subtask-form').style.display = 'block';
                this.style.display = 'none';
            });
            document.getElementById('btn-cancel-subtask').addEventListener('click', function() {
                document.getElementById('subtask-form').style.display = 'none';
                document.getElementById('btn-add-subtask').style.display = '';
            });

            // Status update — custom AJAX to refresh visual state
            document.getElementById('status-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                const form = this;
                const btns = [...form.querySelectorAll('[type="submit"]')];

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: new FormData(form),
                    });
                    const data = await res.json();
                    if (res.ok) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message || 'Status updated.'
                        });
                        form.querySelectorAll('label').forEach(label => {
                            const radio = label.querySelector('input[type="radio"]');
                            const div = label.querySelector('div');
                            if (radio.checked) {
                                div.className = div.className
                                    .replace(
                                        'border-brand-border text-brand-muted hover:border-brand-black/30',
                                        '') +
                                    ' border-brand-black bg-brand-bg text-brand-black';
                            } else {
                                div.className = div.className
                                    .replace('border-brand-black bg-brand-bg text-brand-black', '') +
                                    ' border-brand-border text-brand-muted hover:border-brand-black/30';
                            }
                            div.className = div.className.replace(/\s+/g, ' ').trim();
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Failed to update status.'
                        });
                    }
                } catch {
                    Toast.fire({
                        icon: 'error',
                        title: 'Network error.'
                    });
                }
            });

            // Comment form — insert new comment into DOM on success
            document.getElementById('comment-form').addEventListener('ajax:success', function(e) {
                const c = e.detail.comment;
                if (!c) return;
                document.getElementById('comments-empty')?.remove();
                const html = `<div class="px-4 py-3 border-b border-brand-border/60">
        <div class="flex items-center gap-2 mb-1">
            <div class="w-5 h-5 rounded-full bg-brand-bg border border-brand-border flex items-center justify-center text-[8px] font-semibold text-brand-muted">${escHtml(c.user_initial)}</div>
            <span class="text-[11px] font-medium text-brand-black">${escHtml(c.user_name)}</span>
            <span class="text-[10px] text-brand-subtle">${escHtml(c.created_at)}</span>
        </div>
        <p class="text-[12px] text-brand-black pl-7 whitespace-pre-wrap">${escHtml(c.body)}</p>
    </div>`;
                document.getElementById('comments-list').insertAdjacentHTML('afterbegin', html);
                const count = document.querySelectorAll('#comments-list > div').length;
                document.getElementById('comment-count').textContent = `(${count})`;
            });

            // Subtask form — insert new subtask row into DOM on success
            document.getElementById('subtask-inner-form').addEventListener('ajax:success', function(e) {
                const s = e.detail.subtask;
                if (!s) return;
                document.getElementById('subtasks-empty')?.remove();
                const html = `<div class="data-row flex items-center gap-3">
        <div class="dot-gray"></div>
        <div class="flex-1 min-w-0">
            <a href="${escHtml(s.show_url)}" class="text-[12px] font-medium text-brand-black hover:underline truncate block">${escHtml(s.title)}</a>
            <span class="text-[10px] text-brand-subtle font-mono">${escHtml(s.task_number)}</span>
        </div>
        <span class="text-[11px] text-brand-muted flex-shrink-0">${escHtml(s.due_date)}</span>
    </div>`;
                document.getElementById('subtasks-list').insertAdjacentHTML('beforeend', html);
                const count = document.querySelectorAll('#subtasks-list .data-row').length;
                document.getElementById('subtask-count').textContent = `(${count})`;
                document.getElementById('subtask-form').style.display = 'none';
                document.getElementById('btn-add-subtask').style.display = '';
            });
        </script>
    @endpush
@endsection
