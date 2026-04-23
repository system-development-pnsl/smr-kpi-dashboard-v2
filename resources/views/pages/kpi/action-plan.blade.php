@extends('layouts.app')
@section('title', 'Action Plan — ' . $plan->quarter_label)
@section('page-title', 'Action Plan')
@section('page-sub', $plan->quarter_label . ' · ' . $plan->department->label)

@section('content')
<div class="space-y-5">

    {{-- Header Card --}}
    <div class="card">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded text-white uppercase"
                          style="background-color: {{ $plan->department->color }}">
                        {{ $plan->department->code }}
                    </span>
                    <span class="text-[12px] font-semibold text-brand-black">{{ $plan->department->label }}</span>
                    <span class="text-[11px] text-brand-muted">·</span>
                    <span class="text-[11px] text-brand-muted">{{ $plan->quarter_label }}</span>
                </div>
                @if($plan->mission)
                    <p class="text-[13px] text-brand-black font-medium leading-relaxed mb-3">{{ $plan->mission }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-4 text-[11px] text-brand-muted">
                    @if($plan->creator)
                        <span>Created by <span class="text-brand-black">{{ $plan->creator->full_name }}</span></span>
                    @endif
                    @if($plan->submitted_at)
                        <span>Submitted {{ $plan->submitted_at->format('d M Y') }}</span>
                    @endif
                    @if($plan->acknowledged_at)
                        <span class="flex items-center gap-1 text-status-green">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Acknowledged {{ $plan->acknowledged_at->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex-shrink-0 text-right">
                <span class="text-[28px] font-bold text-brand-black leading-none">{{ $plan->overall_progress }}%</span>
                <p class="text-[10px] text-brand-muted mt-0.5">overall progress</p>
                <div class="progress-bar mt-2 w-24">
                    <div class="progress-bar-fill
                        @if($plan->overall_progress >= 80) bg-status-green
                        @elseif($plan->overall_progress >= 50) bg-status-amber
                        @else bg-status-red @endif"
                         style="width: {{ $plan->overall_progress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Goals --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-[13px] font-semibold text-brand-black">Strategic Goals
                <span class="ml-1.5 text-[11px] font-normal text-brand-muted">({{ $plan->goals->count() }})</span>
            </h2>
            <button type="button" id="btn-add-goal"
                    class="btn-primary text-[11px] py-1.5 px-3">
                + Add Goal
            </button>
        </div>

        {{-- Add Goal Form (hidden by default) --}}
        <div id="add-goal-form" class="card space-y-3 hidden">
            <p class="text-[12px] font-semibold text-brand-black">New Strategic Goal</p>
            <div>
                <label class="label">Goal Title <span class="text-status-red">*</span></label>
                <input type="text" id="goal-title" class="input" placeholder="e.g. Improve Guest Satisfaction Score" maxlength="200">
            </div>
            <div>
                <label class="label">Description</label>
                <textarea id="goal-desc" rows="2" class="textarea" placeholder="Optional context or success criteria…" maxlength="500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label">Target Date <span class="text-status-red">*</span></label>
                    <input type="date" id="goal-date" class="input">
                </div>
                <div>
                    <label class="label">Goal Owner <span class="text-status-red">*</span></label>
                    <select id="goal-owner" class="input">
                        <option value="">— Select —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button type="button" id="btn-save-goal" class="btn-primary text-[11px] py-1.5 px-4">Save Goal</button>
                <button type="button" id="btn-cancel-goal" class="btn-secondary text-[11px] py-1.5 px-4">Cancel</button>
                <span id="goal-error" class="text-[11px] text-status-red hidden"></span>
            </div>
        </div>

        @forelse($plan->goals as $goal)
            @php
                $statusMap = [
                    'on_track'  => ['label' => 'On Track',   'dot' => 'dot-green', 'bar' => 'bg-status-green'],
                    'at_risk'   => ['label' => 'At Risk',    'dot' => 'dot-amber', 'bar' => 'bg-status-amber'],
                    'off_track' => ['label' => 'Off Track',  'dot' => 'dot-red',   'bar' => 'bg-status-red'],
                    'completed' => ['label' => 'Completed',  'dot' => 'dot-blue',  'bar' => 'bg-status-blue'],
                ];
                $s = $statusMap[$goal->status] ?? ['label' => $goal->status, 'dot' => 'dot-gray', 'bar' => 'bg-brand-muted'];
            @endphp
            <div class="card space-y-4">
                {{-- Goal Header --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <div class="{{ $s['dot'] }}"></div>
                            <span class="text-[13px] font-semibold text-brand-black">{{ $goal->title }}</span>
                            <span class="text-[10px] text-brand-muted border border-brand-border px-1.5 py-0.5 rounded">{{ $s['label'] }}</span>
                        </div>
                        @if($goal->description)
                            <p class="text-[11px] text-brand-muted leading-relaxed">{{ $goal->description }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-[11px] text-brand-muted">
                            @if($goal->owner)
                                <span>Owner: <span class="text-brand-black">{{ $goal->owner->full_name ?? $goal->owner }}</span></span>
                            @endif
                            @if($goal->target_date)
                                <span>Due: <span class="text-brand-black">{{ $goal->target_date->format('d M Y') }}</span></span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <span class="text-[20px] font-bold text-brand-black leading-none">{{ $goal->action_items_progress }}%</span>
                        <p class="text-[9px] text-brand-muted">progress</p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="progress-bar">
                    <div class="progress-bar-fill {{ $s['bar'] }}" style="width: {{ $goal->action_items_progress }}%"></div>
                </div>

                {{-- Action Items --}}
                @if($goal->actionItems->count())
                    <div class="space-y-2">
                        <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide">Action Items</p>
                        <div class="divide-y divide-brand-border/50">
                            @foreach($goal->actionItems as $item)
                                @php
                                    $itemStatus = match($item->status) {
                                        'completed'   => ['text' => 'text-status-green', 'bg' => 'bg-status-green'],
                                        'in_progress' => ['text' => 'text-status-blue',  'bg' => 'bg-status-blue'],
                                        default       => ['text' => 'text-brand-muted',  'bg' => 'bg-brand-muted'],
                                    };
                                @endphp
                                <div class="flex items-center gap-3 py-2.5">
                                    {{-- Completion ring --}}
                                    <div class="flex-shrink-0 relative w-8 h-8">
                                        <svg class="w-8 h-8 -rotate-90" viewBox="0 0 32 32">
                                            <circle cx="16" cy="16" r="12" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                                            <circle cx="16" cy="16" r="12" fill="none"
                                                    stroke="{{ $item->completion_pct >= 100 ? '#16a34a' : ($item->completion_pct > 0 ? '#2563eb' : '#9ca3af') }}"
                                                    stroke-width="3"
                                                    stroke-dasharray="{{ round(2 * 3.14159 * 12, 1) }}"
                                                    stroke-dashoffset="{{ round(2 * 3.14159 * 12 * (1 - $item->completion_pct / 100), 1) }}"
                                                    stroke-linecap="round"/>
                                        </svg>
                                        <span class="absolute inset-0 flex items-center justify-center text-[8px] font-bold {{ $itemStatus['text'] }}">
                                            {{ $item->completion_pct }}%
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[12px] text-brand-black {{ $item->completion_pct >= 100 ? 'line-through text-brand-muted' : '' }}">
                                            {{ $item->description }}
                                        </p>
                                        <div class="flex flex-wrap gap-3 mt-0.5 text-[10px] text-brand-muted">
                                            @if($item->assignee)
                                                <span>{{ $item->assignee->full_name }}</span>
                                            @endif
                                            @if($item->due_date)
                                                <span class="{{ $item->due_date->isPast() && $item->completion_pct < 100 ? 'text-status-red' : '' }}">
                                                    Due {{ $item->due_date->format('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="h-1.5 w-16 bg-brand-border rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $itemStatus['bg'] }}"
                                                 style="width: {{ $item->completion_pct }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-[11px] text-brand-subtle">No action items yet.</p>
                @endif
            </div>
        @empty
            <div class="card text-center py-10 text-brand-muted text-[13px]">
                No strategic goals defined yet. Click <strong>Add Goal</strong> to get started.
            </div>
        @endforelse
    </div>

    {{-- Back --}}
    <div>
        <a href="{{ route('kpi.index') }}" class="btn-secondary text-[11px]">← Back to KPI Dashboard</a>
    </div>
</div>

@push('scripts')
<script>
    const addGoalForm   = document.getElementById('add-goal-form');
    const btnAddGoal    = document.getElementById('btn-add-goal');
    const btnCancelGoal = document.getElementById('btn-cancel-goal');
    const btnSaveGoal   = document.getElementById('btn-save-goal');
    const goalError     = document.getElementById('goal-error');

    btnAddGoal.addEventListener('click', () => {
        addGoalForm.classList.remove('hidden');
        btnAddGoal.classList.add('hidden');
        document.getElementById('goal-title').focus();
    });

    btnCancelGoal.addEventListener('click', () => {
        addGoalForm.classList.add('hidden');
        btnAddGoal.classList.remove('hidden');
        goalError.classList.add('hidden');
    });

    btnSaveGoal.addEventListener('click', async () => {
        const title    = document.getElementById('goal-title').value.trim();
        const desc     = document.getElementById('goal-desc').value.trim();
        const date     = document.getElementById('goal-date').value;
        const ownerId  = document.getElementById('goal-owner').value;

        goalError.classList.add('hidden');

        if (!title || !date || !ownerId) {
            goalError.textContent = 'Title, target date, and owner are required.';
            goalError.classList.remove('hidden');
            return;
        }

        btnSaveGoal.disabled = true;
        btnSaveGoal.textContent = 'Saving…';

        try {
            const res = await fetch('{{ route('kpi.goal.store', $plan) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ title, description: desc, target_date: date, owner_id: ownerId }),
            });

            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                goalError.textContent = data.message ?? 'Failed to save goal.';
                goalError.classList.remove('hidden');
            }
        } catch {
            goalError.textContent = 'Network error. Please try again.';
            goalError.classList.remove('hidden');
        } finally {
            btnSaveGoal.disabled = false;
            btnSaveGoal.textContent = 'Save Goal';
        }
    });
</script>
@endpush
@endsection
