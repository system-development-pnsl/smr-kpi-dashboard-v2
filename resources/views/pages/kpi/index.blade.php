@extends('layouts.app')
@section('title', 'Operations Overview')
@section('page-title', 'Operations Overview')
@section('page-sub', 'Department performance & targets — ' . now()->format('F Y'))

@section('content')
<div id="kpi-container" style="display:flex;flex-direction:column;gap:16px;">

    {{-- ── Row: Status Overview ─────────────────────────────────── --}}
    <div id="row-kpi-overview" draggable="true" class="kpi-row">
        <div class="flex items-center gap-2 mb-3">
            <div class="dash-grip" title="Drag to reorder"
                 style="cursor:grab;color:#d4d4d4;display:flex;align-items:center;flex-shrink:0;">
                <svg style="width:11px;height:15px;" fill="currentColor" viewBox="0 0 10 16">
                    <circle cx="2.5" cy="2"  r="1.3"/><circle cx="7.5" cy="2"  r="1.3"/>
                    <circle cx="2.5" cy="8"  r="1.3"/><circle cx="7.5" cy="8"  r="1.3"/>
                    <circle cx="2.5" cy="14" r="1.3"/><circle cx="7.5" cy="14" r="1.3"/>
                </svg>
            </div>
            <h2 class="text-[13px] font-semibold text-brand-black flex-1">Status Overview</h2>
            <button data-collapse-toggle="kpi-overview"
                    class="cursor-pointer text-brand-muted hover:text-brand-black transition-colors p-1 rounded hover:bg-brand-bg">
                <svg data-chevron class="w-[14px] h-[14px] transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>
        <div id="kpi-overview">
            <div class="grid grid-cols-3 gap-3 mb-3">
                @foreach([
                    ['label' => 'On Track',    'count' => $summary['on_track'],  'class' => 'text-status-green bg-status-green-bg border-status-green/20', 'key' => 'on_track'],
                    ['label' => 'Near Target', 'count' => $summary['at_risk'],   'class' => 'text-status-amber bg-status-amber-bg border-status-amber/20', 'key' => 'near_target'],
                    ['label' => 'Off Track',   'count' => $summary['off_track'], 'class' => 'text-status-red   bg-status-red-bg   border-status-red/20',   'key' => 'off_track'],
                ] as $s)
                    <a href="{{ route('kpi.index', ['status' => $s['key']]) }}"
                       class="flex flex-col items-center py-3 rounded-xl border font-medium transition-all {{ $s['class'] }}
                               {{ request('status') === $s['key'] ? 'ring-2 ring-offset-1 ring-current/30' : '' }}">
                        <span class="text-[22px] font-bold leading-none">{{ $s['count'] }}</span>
                        <span class="text-[11px] mt-1 opacity-80">{{ $s['label'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <svg class="w-[13px] h-[13px] text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                @foreach(['All', 'On Track', 'Near Target', 'Off Track'] as $f)
                    <a href="{{ route('kpi.index', array_merge(request()->query(), ['status' => $f === 'All' ? '' : strtolower(str_replace(' ', '_', $f))])) }}"
                       class="text-[11px] font-medium px-3 py-1 rounded-full border transition-colors
                              {{ (request('status', '') === ($f === 'All' ? '' : strtolower(str_replace(' ', '_', $f)))) ? 'bg-brand-black text-white border-brand-black' : 'border-brand-border text-brand-muted hover:border-brand-black hover:text-brand-black' }}">
                        {{ $f }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Row: KPI Metrics ─────────────────────────────────────── --}}
    <div id="row-kpi-metrics" draggable="true" class="kpi-row">
        <div class="flex items-center gap-2 mb-3">
            <div class="dash-grip" title="Drag to reorder"
                 style="cursor:grab;color:#d4d4d4;display:flex;align-items:center;flex-shrink:0;">
                <svg style="width:11px;height:15px;" fill="currentColor" viewBox="0 0 10 16">
                    <circle cx="2.5" cy="2"  r="1.3"/><circle cx="7.5" cy="2"  r="1.3"/>
                    <circle cx="2.5" cy="8"  r="1.3"/><circle cx="7.5" cy="8"  r="1.3"/>
                    <circle cx="2.5" cy="14" r="1.3"/><circle cx="7.5" cy="14" r="1.3"/>
                </svg>
            </div>
            <h2 class="text-[13px] font-semibold text-brand-black flex-1">
                Operations Overview
                <span class="ml-1.5 text-[11px] font-normal text-brand-muted">({{ $kpis->count() }})</span>
            </h2>
            <button data-collapse-toggle="kpi-metrics"
                    class="cursor-pointer text-brand-muted hover:text-brand-black transition-colors p-1 rounded hover:bg-brand-bg">
                <svg data-chevron class="w-[14px] h-[14px] transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>
        <div id="kpi-metrics">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-3">
                @foreach($kpis as $kpi)
                    @include('components.kpi-card', ['kpi' => $kpi])
                @endforeach
            </div>
            @if($kpis->isEmpty())
                <div class="text-center py-16 text-brand-muted text-[13px]">
                    No KPIs match the selected filter.
                </div>
            @endif

            {{-- Hidden cards recovery bar --}}
            <div id="kpi-hidden-bar" class="mt-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-brand-bg border border-brand-border" style="display:none!important;">
                <svg class="w-[13px] h-[13px] text-brand-muted flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                </svg>
                <span class="text-[11px] text-brand-muted flex-1">
                    <span id="kpi-hidden-count">0</span> card(s) hidden
                </span>
                <button id="kpi-show-all-btn" type="button"
                        class="text-[11px] font-medium text-brand-black hover:underline">
                    Show all
                </button>
            </div>
        </div>
    </div>

    {{-- ── Row: Department Action Plans ────────────────────────── --}}
    <div id="row-kpi-plans" draggable="true" class="kpi-row">
        <div class="flex items-center gap-2 mb-3">
            <div class="dash-grip" title="Drag to reorder"
                 style="cursor:grab;color:#d4d4d4;display:flex;align-items:center;flex-shrink:0;">
                <svg style="width:11px;height:15px;" fill="currentColor" viewBox="0 0 10 16">
                    <circle cx="2.5" cy="2"  r="1.3"/><circle cx="7.5" cy="2"  r="1.3"/>
                    <circle cx="2.5" cy="8"  r="1.3"/><circle cx="7.5" cy="8"  r="1.3"/>
                    <circle cx="2.5" cy="14" r="1.3"/><circle cx="7.5" cy="14" r="1.3"/>
                </svg>
            </div>
            <h2 class="text-[13px] font-semibold text-brand-black flex-1">
                Department Action Plans
                <span class="ml-1.5 text-[11px] font-normal text-brand-muted">({{ $actionPlans->count() }})</span>
            </h2>
            <button data-collapse-toggle="kpi-plans"
                    class="cursor-pointer text-brand-muted hover:text-brand-black transition-colors p-1 rounded hover:bg-brand-bg">
                <svg data-chevron class="w-[14px] h-[14px] transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>
        <div id="kpi-plans">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($actionPlans as $plan)
                    <div class="card space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white uppercase"
                                          style="background-color: {{ $plan->department->color }}">
                                        {{ $plan->department->code }}
                                    </span>
                                    <span class="text-[12px] font-semibold text-brand-black">{{ $plan->department->label }}</span>
                                </div>
                                @if($plan->mission)
                                    <p class="text-[11px] text-brand-muted leading-relaxed line-clamp-2">{{ $plan->mission }}</p>
                                @endif
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <span class="text-[20px] font-bold text-brand-black">{{ $plan->overall_progress }}%</span>
                                <p class="text-[9px] text-brand-muted">overall</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            @foreach($plan->goals as $goal)
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11px] text-brand-black truncate">{{ $goal->title }}</span>
                                        <span class="text-[10px] font-medium text-brand-muted ml-2 flex-shrink-0">
                                            {{ $goal->action_items_progress }}%
                                        </span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-bar-fill
                                                    {{ ['on_track' => 'bg-status-green', 'at_risk' => 'bg-status-amber', 'off_track' => 'bg-status-red', 'completed' => 'bg-status-blue'][$goal->status] ?? 'bg-brand-muted' }}"
                                             style="width: {{ $goal->action_items_progress }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-brand-border">
                            <span class="text-[10px] text-brand-subtle">{{ $plan->quarter_label }}</span>
                            <a href="{{ route('kpi.action-plan', $plan) }}"
                               class="text-[11px] text-brand-muted hover:text-brand-black font-medium transition-colors">
                                View details →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($actionPlans->isEmpty())
                <div class="text-center py-10 text-brand-muted text-[13px]">No action plans for this quarter.</div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('kpi-container')
    const LS_KEY    = 'kpi_row_order_v1'

    // ── Restore saved order ───────────────────────────────────────
    try {
        const saved = JSON.parse(localStorage.getItem(LS_KEY))
        if (Array.isArray(saved)) {
            saved.forEach(id => {
                const el = document.getElementById(id)
                if (el) container.appendChild(el)
            })
        }
    } catch {}

    function saveOrder() {
        const order = [...container.children]
            .filter(el => el.classList.contains('kpi-row'))
            .map(el => el.id)
        localStorage.setItem(LS_KEY, JSON.stringify(order))
    }

    // ── Drag-and-drop ─────────────────────────────────────────────
    const dropLine = document.createElement('div')
    dropLine.style.cssText = `height:3px;background:#2563eb;border-radius:2px;
                               margin:2px 0;display:none;pointer-events:none;flex-shrink:0;`
    container.appendChild(dropLine)

    let dragging = null

    document.querySelectorAll('.kpi-row').forEach(row => {

        row.addEventListener('dragstart', e => {
            dragging = row
            e.dataTransfer.effectAllowed = 'move'
            e.dataTransfer.setData('text/plain', row.id)
            setTimeout(() => {
                row.style.opacity       = '0.4'
                row.style.outline       = '2px dashed #e5e5e3'
                row.style.outlineOffset = '2px'
            }, 0)
        })

        row.addEventListener('dragend', () => {
            row.style.opacity       = ''
            row.style.outline       = ''
            row.style.outlineOffset = ''
            dropLine.style.display  = 'none'
            dragging = null
        })

        row.addEventListener('dragover', e => {
            e.preventDefault()
            e.dataTransfer.dropEffect = 'move'
            if (!dragging || dragging === row) return
            const rect   = row.getBoundingClientRect()
            const before = e.clientY < rect.top + rect.height / 2
            container.insertBefore(dropLine, before ? row : row.nextSibling)
            dropLine.style.display = 'block'
        })

        row.addEventListener('drop', e => {
            e.preventDefault()
            if (!dragging || dragging === row) return
            container.insertBefore(dragging, dropLine)
            dropLine.style.display = 'none'
            saveOrder()
        })
    })

    container.addEventListener('dragleave', e => {
        if (!container.contains(e.relatedTarget)) {
            dropLine.style.display = 'none'
        }
    })

    // Block drag on interactive children
    container.addEventListener('dragstart', e => {
        if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' ||
            e.target.tagName === 'INPUT') {
            e.preventDefault()
        }
    }, true)
})
</script>
@endpush
@endsection
