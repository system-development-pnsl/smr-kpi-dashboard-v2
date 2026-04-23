{{-- resources/views/components/task-row.blade.php --}}
@php
    $priorityConfig = [
        'P1' => ['label' => 'Urgent', 'class' => 'badge-red'],
        'P2' => ['label' => 'High',   'class' => 'badge-amber'],
        'P3' => ['label' => 'Normal', 'class' => 'badge-blue'],
        'P4' => ['label' => 'Low',    'class' => 'badge-gray'],
    ];
    $statusDot = [
        'TODO'        => 'dot-gray',
        'IN_PROGRESS' => 'dot-blue',
        'BLOCKED'     => 'dot-red',
        'DONE'        => 'dot-green',
        'CANCELLED'   => 'dot-gray',
    ];
    $prio    = $priorityConfig[$task->priority] ?? $priorityConfig['P3'];
    $isOver  = $task->is_overdue ?? false;
@endphp

<div class="data-row group {{ $isOver ? 'bg-status-red-bg/30 border-l-2 border-l-status-red' : '' }}">
    {{-- Status dot --}}
    <div class="{{ $statusDot[$task->status] ?? 'dot-gray' }} flex-shrink-0"></div>

    {{-- Task info --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
            <a href="{{ route('tasks.show', $task) }}"
               class="text-[12px] font-medium text-brand-black hover:underline truncate">
                {{ $task->title }}
            </a>
            @if($isOver)
                <svg class="w-[11px] h-[11px] text-status-red flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            @endif
        </div>
        <div class="flex items-center gap-1.5 mt-0.5">
            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide text-white"
                  style="background-color: {{ $task->department->color ?? '#737373' }}">
                {{ $task->department->code ?? 'N/A' }}
            </span>
            <span class="text-[11px] text-brand-muted truncate">{{ $task->category }}</span>
        </div>
    </div>

    {{-- Priority --}}
    <span class="{{ $prio['class'] }} flex-shrink-0">{{ $prio['label'] }}</span>

    {{-- Assignee --}}
    <span class="hidden sm:block text-[11px] text-brand-muted truncate max-w-[90px]">
        {{ $task->assignees->pluck('full_name')->first() ?? '—' }}
    </span>

    {{-- Due date --}}
    <span class="hidden md:block text-[11px] flex-shrink-0 {{ $isOver ? 'text-status-red font-medium' : 'text-brand-muted' }}">
        {{ $task->due_date->format('d M Y') }}
    </span>

    {{-- Task number --}}
    <span class="hidden lg:block text-[10px] text-brand-subtle font-mono">{{ $task->task_number }}</span>
</div>


{{-- ============================================================ --}}
{{-- resources/views/components/notification-item.blade.php       --}}
{{-- ============================================================ --}}
