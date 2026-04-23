@extends('layouts.app')
@section('title', isset($task) ? 'Edit Task' : 'New Task')
@section('page-title', isset($task) ? 'Edit Task' : 'Create New Task')
@section('page-sub', 'Task Management')

@section('content')
<div>
    <form method="POST"
          action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}"
          class="space-y-5">
        @csrf
        @if(isset($task)) @method('PUT') @endif

        <div class="card space-y-5">

            {{-- Task Title --}}
            <div>
                <label class="label">Task Title <span class="text-status-red">*</span></label>
                <input type="text" name="title" value="{{ old('title', isset($task) ? $task->title : '') }}"
                       class="input @error('title') border-status-red @enderror"
                       placeholder="e.g. Monthly HVAC Inspection — Floor 3" maxlength="200" required>
                @error('title')
                    <p class="mt-1 text-[11px] text-status-red">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="label">Task Details</label>
                <textarea name="description" rows="3"
                          class="textarea @error('description') border-status-red @enderror"
                          placeholder="Add context, steps, or acceptance criteria…" maxlength="1000"
                >{{ old('description', isset($task) ? $task->description : '') }}</textarea>
                <p class="mt-1 text-[10px] text-brand-subtle">Optional · max 1000 characters</p>
            </div>

            {{-- Department + Category --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Department <span class="text-status-red">*</span></label>
                    <x-select-search
                        name="department_id"
                        :options="$departments->map(fn($d) => ['value' => $d->id, 'label' => $d->code.' — '.$d->label])->all()"
                        placeholder="— Select —"
                        :selected="old('department_id', isset($task) ? $task->department_id : '')"
                        :required="true"
                        class="w-full"
                    />
                    @error('department_id')
                        <p class="mt-1 text-[11px] text-status-red">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="label">Category</label>
                    <input type="text" name="category"
                           value="{{ old('category', isset($task) ? $task->category : '') }}"
                           class="input" placeholder="e.g. PPM Schedule">
                </div>
            </div>

            {{-- Assignees --}}
            <div>
                <label class="label">Assign To <span class="text-status-red">*</span></label>
                <x-select-search-multi
                    name="assignee_ids[]"
                    :options="$users->map(fn($u) => ['value' => $u->id, 'label' => $u->full_name.' — '.($u->department->code ?? 'N/A')])->all()"
                    placeholder="Select assignees…"
                    :selected="old('assignee_ids', isset($task) ? $task->assignees->pluck('id')->toArray() : [])"
                    :required="true"
                    class="w-full"
                />
                @error('assignee_ids')
                    <p class="mt-1 text-[11px] text-status-red">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start + Due Date --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Start Date</label>
                    <input type="date" name="start_date"
                           value="{{ old('start_date', isset($task) ? $task->start_date?->format('Y-m-d') : today()->format('Y-m-d')) }}"
                           class="input">
                </div>
                <div>
                    <label class="label">Due Date <span class="text-status-red">*</span></label>
                    <input type="date" name="due_date"
                           value="{{ old('due_date', isset($task) ? $task->due_date?->format('Y-m-d') : '') }}"
                           class="input @error('due_date') border-status-red @enderror" required>
                    @error('due_date')
                        <p class="mt-1 text-[11px] text-status-red">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Priority --}}
            <div>
                <label class="label">Priority</label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach(['P1' => ['Urgent', 'border-status-red bg-status-red-bg text-status-red'], 'P2' => ['High', 'border-status-amber bg-status-amber-bg text-status-amber'], 'P3' => ['Normal', 'border-status-blue bg-status-blue-bg text-status-blue'], 'P4' => ['Low', 'border-brand-border bg-brand-bg text-brand-muted']] as $val => [$label, $activeClass])
                        <label class="cursor-pointer">
                            <input type="radio" name="priority" value="{{ $val }}" class="sr-only"
                                   {{ old('priority', isset($task) ? $task->priority : 'P3') === $val ? 'checked' : '' }}>
                            <div class="text-[11px] font-semibold text-center py-2 rounded-lg border transition-all
                                        {{ old('priority', isset($task) ? $task->priority : 'P3') === $val ? $activeClass : 'border-brand-border text-brand-muted hover:border-brand-black/30' }}">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="label">Status <span class="text-status-red">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach(['TODO' => ['To Do', 'dot-gray'], 'IN_PROGRESS' => ['In Progress', 'dot-blue'], 'BLOCKED' => ['Blocked', 'dot-red'], 'DONE' => ['Done', 'dot-green']] as $val => [$label, $dotClass])
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="{{ $val }}" class="sr-only"
                                   {{ old('status', isset($task) ? $task->status : 'TODO') === $val ? 'checked' : '' }}>
                            <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg border transition-all
                                        {{ old('status', isset($task) ? $task->status : 'TODO') === $val ? 'border-brand-black bg-brand-bg' : 'border-brand-border hover:border-brand-black/30' }}">
                                <div class="{{ $dotClass }}"></div>
                                <span class="text-[11px] font-medium text-brand-black">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Tags --}}
            <div>
                <label class="label">Tags</label>
                <input type="text" name="tags_input"
                       value="{{ old('tags_input', isset($task) ? implode(', ', is_array($task->tags) ? $task->tags : (json_decode($task->tags, true) ?? [])) : '') }}"
                       class="input" placeholder="e.g. maintenance, urgent, hvac">
                <p class="mt-1 text-[10px] text-brand-subtle">Comma-separated</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('tasks.index') }}" class="btn-secondary">Cancel</a>
            <div class="flex gap-2">
                @if(isset($task))
                    <button type="button" id="btn-delete-task" class="btn-danger">Delete</button>
                @endif
                <button type="submit" class="btn-primary">
                    {{ isset($task) ? 'Update Task' : 'Create Task' }}
                </button>
            </div>
        </div>
    </form>

    @if(isset($task))
    <form id="delete-task-form" method="POST" action="{{ route('tasks.destroy', $task) }}" style="display:none">
        @csrf @method('DELETE')
    </form>
    @endif
</div>

@push('scripts')
<script>
    // Highlight priority radio on click
    document.querySelectorAll('input[name="priority"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="priority"]').forEach(r => {
                r.nextElementSibling.className = r.nextElementSibling.className
                    .replace(/border-status-\w+\s+bg-status-[\w-]+\s+text-status-\w+|border-brand-border\s+bg-brand-bg\s+text-brand-muted/, '')
                    + ' border-brand-border text-brand-muted hover:border-brand-black/30'
            })
            const activeClasses = {
                P1: 'border-status-red bg-status-red-bg text-status-red',
                P2: 'border-status-amber bg-status-amber-bg text-status-amber',
                P3: 'border-status-blue bg-status-blue-bg text-status-blue',
                P4: 'border-brand-border bg-brand-bg text-brand-muted',
            }
            this.nextElementSibling.classList.add(...activeClasses[this.value].split(' '))
        })
    })

    // Highlight status radio on click
    document.querySelectorAll('input[name="status"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="status"]').forEach(r => {
                r.nextElementSibling.className = r.nextElementSibling.className
                    .replace('border-brand-black bg-brand-bg', 'border-brand-border')
            })
            this.nextElementSibling.classList.add('border-brand-black', 'bg-brand-bg')
        })
    })

    // Delete button — confirm then submit the separate delete form
    const btnDelete = document.getElementById('btn-delete-task');
    if (btnDelete) {
        btnDelete.addEventListener('click', async function () {
            const { isConfirmed } = await Swal.fire({
                title: 'Delete this task?',
                text: 'This task will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
            });
            if (isConfirmed) {
                document.getElementById('delete-task-form').submit();
            }
        });
    }
</script>
@endpush
@endsection
