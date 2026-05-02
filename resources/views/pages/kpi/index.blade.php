@extends('layouts.app')
@section('title', 'Operations Overview')
@section('page-title', 'Operations Overview')
@section('page-sub', 'Department performance & targets — ' . now()->format('F Y'))

@section('content')
<div style="display:flex;flex-direction:column;gap:8px;">

    {{-- ── Row: Department Action Plans (TOP) ─────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
        <button class="fin-toggle" data-target="kpi-plans"
                style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
            <div class="fin-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .15s;">
                <svg class="fin-arrow" style="width:9px;height:9px;transition:transform .2s;transform:rotate(90deg);" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 3l14 9-14 9V3z"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Department Action Plans <span style="font-size:11px;font-weight:400;color:#a3a3a3;">({{ $actionPlans->count() + 6 }})</span></p>
                <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Goals, tasks &amp; progress by department</p>
            </div>
        </button>
        <div id="kpi-plans" style="border-top:1px solid #f0f0ef;padding:16px 20px 20px;">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">

                {{-- DB-driven plans --}}
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
                                        <span class="text-[10px] font-medium text-brand-muted ml-2 flex-shrink-0">{{ $goal->action_items_progress }}%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-bar-fill {{ ['on_track'=>'bg-status-green','at_risk'=>'bg-status-amber','off_track'=>'bg-status-red','completed'=>'bg-status-blue'][$goal->status] ?? 'bg-brand-muted' }}"
                                             style="width:{{ $goal->action_items_progress }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-brand-border">
                            <span class="text-[10px] text-brand-subtle">{{ $plan->quarter_label }}</span>
                            <a href="{{ route('kpi.action-plan', $plan) }}" class="text-[11px] text-brand-muted hover:text-brand-black font-medium transition-colors">View details →</a>
                        </div>
                    </div>
                @endforeach

                {{-- Static sample plans ──────────────────────────────── --}}
                @php
                $samplePlans = [
                    [
                        'code' => 'HR', 'color' => '#7c3aed', 'label' => 'Human Resources',
                        'mission' => 'Develop talent, improve staff satisfaction & reduce turnover rate.',
                        'progress' => 67, 'quarter' => 'Q2 2025',
                        'goals' => [
                            ['title' => 'Reduce staff turnover rate',       'pct' => 75, 'status' => 'on_track'],
                            ['title' => 'Complete performance reviews',      'pct' => 45, 'status' => 'at_risk'],
                            ['title' => 'Conduct monthly training programs', 'pct' => 80, 'status' => 'on_track'],
                        ],
                    ],
                    [
                        'code' => 'HK', 'color' => '#0891b2', 'label' => 'Housekeeping',
                        'mission' => 'Maintain room quality standards and achieve top guest satisfaction scores.',
                        'progress' => 71, 'quarter' => 'Q2 2025',
                        'goals' => [
                            ['title' => 'Achieve 95% room cleanliness score', 'pct' => 88, 'status' => 'on_track'],
                            ['title' => 'Reduce checkout-to-ready time',      'pct' => 52, 'status' => 'at_risk'],
                            ['title' => 'Complete deep-cleaning schedule',    'pct' => 72, 'status' => 'on_track'],
                        ],
                    ],
                    [
                        'code' => 'FNB', 'color' => '#d97706', 'label' => 'Food & Beverage',
                        'mission' => 'Deliver exceptional dining experiences while controlling food cost.',
                        'progress' => 62, 'quarter' => 'Q2 2025',
                        'goals' => [
                            ['title' => 'Reduce food cost to 28%',       'pct' => 35, 'status' => 'off_track'],
                            ['title' => 'Achieve F&B monthly revenue',   'pct' => 81, 'status' => 'on_track'],
                            ['title' => 'Launch seasonal menu updates',  'pct' => 70, 'status' => 'on_track'],
                        ],
                    ],
                    [
                        'code' => 'FO', 'color' => '#16a34a', 'label' => 'Front Office',
                        'mission' => 'Deliver seamless check-in experience and maximise room upsell revenue.',
                        'progress' => 74, 'quarter' => 'Q2 2025',
                        'goals' => [
                            ['title' => 'Achieve 95% check-in satisfaction', 'pct' => 87, 'status' => 'on_track'],
                            ['title' => 'Upsell room upgrades target',        'pct' => 48, 'status' => 'at_risk'],
                            ['title' => 'Reduce avg. wait time < 3 min',     'pct' => 76, 'status' => 'on_track'],
                        ],
                    ],
                    [
                        'code' => 'MNT', 'color' => '#64748b', 'label' => 'Maintenance',
                        'mission' => 'Ensure all facilities meet safety & operational standards at all times.',
                        'progress' => 69, 'quarter' => 'Q2 2025',
                        'goals' => [
                            ['title' => 'Complete preventive maintenance plan', 'pct' => 82, 'status' => 'on_track'],
                            ['title' => 'Work order response < 2 hours',        'pct' => 55, 'status' => 'at_risk'],
                            ['title' => 'Zero safety-compliance failures',      'pct' => 100,'status' => 'on_track'],
                        ],
                    ],
                    [
                        'code' => 'S&M', 'color' => '#dc2626', 'label' => 'Sales & Marketing',
                        'mission' => 'Drive revenue growth, increase direct bookings & expand corporate accounts.',
                        'progress' => 58, 'quarter' => 'Q2 2025',
                        'goals' => [
                            ['title' => 'Achieve monthly revenue target',   'pct' => 91, 'status' => 'on_track'],
                            ['title' => 'Grow direct booking share to 40%', 'pct' => 58, 'status' => 'at_risk'],
                            ['title' => 'Acquire 5 new corporate accounts', 'pct' => 30, 'status' => 'off_track'],
                        ],
                    ],
                ];
                $barColors = ['on_track' => 'bg-status-green', 'at_risk' => 'bg-status-amber', 'off_track' => 'bg-status-red'];
                @endphp

                @foreach($samplePlans as $sp)
                    <div class="card space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white uppercase"
                                          style="background-color:{{ $sp['color'] }}">{{ $sp['code'] }}</span>
                                    <span class="text-[12px] font-semibold text-brand-black">{{ $sp['label'] }}</span>
                                </div>
                                <p class="text-[11px] text-brand-muted leading-relaxed line-clamp-2">{{ $sp['mission'] }}</p>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <span class="text-[20px] font-bold text-brand-black">{{ $sp['progress'] }}%</span>
                                <p class="text-[9px] text-brand-muted">overall</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @foreach($sp['goals'] as $g)
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11px] text-brand-black truncate">{{ $g['title'] }}</span>
                                        <span class="text-[10px] font-medium text-brand-muted ml-2 flex-shrink-0">{{ $g['pct'] }}%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-bar-fill {{ $barColors[$g['status']] }}"
                                             style="width:{{ $g['pct'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-brand-border">
                            <span class="text-[10px] text-brand-subtle">{{ $sp['quarter'] }}</span>
                            <span class="text-[11px] text-brand-subtle">Sample data</span>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{-- ── Row: Status Overview ─────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
        <button class="fin-toggle" data-target="kpi-overview"
                style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
            <div class="fin-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .15s;">
                <svg class="fin-arrow" style="width:9px;height:9px;transition:transform .2s;transform:rotate(90deg);" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 3l14 9-14 9V3z"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Status Overview</p>
                <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">On track, near target &amp; off track</p>
            </div>
        </button>
        <div id="kpi-overview" style="border-top:1px solid #f0f0ef;padding:16px 20px 20px;">
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
    <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
        <button class="fin-toggle" data-target="kpi-metrics"
                style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
            <div class="fin-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .15s;">
                <svg class="fin-arrow" style="width:9px;height:9px;transition:transform .2s;transform:rotate(90deg);" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 3l14 9-14 9V3z"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">KPI Metrics <span style="font-size:11px;font-weight:400;color:#a3a3a3;">({{ $kpis->count() }})</span></p>
                <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Department performance &amp; targets</p>
            </div>
        </button>
        <div id="kpi-metrics" style="border-top:1px solid #f0f0ef;padding:16px 20px 20px;">
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


</div>

@push('scripts')
<script>
document.querySelectorAll('.fin-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var body  = document.getElementById(btn.dataset.target);
        var arrow = btn.querySelector('.fin-arrow');
        var wrap  = btn.querySelector('.fin-arrow-wrap');
        if (!body) return;
        var open = body.style.display !== 'none';
        body.style.display    = open ? 'none' : 'block';
        arrow.style.transform = open ? 'rotate(0deg)' : 'rotate(90deg)';
        if (wrap)  wrap.style.background  = open ? '#f3f3f2' : '#0a0a0a';
        if (arrow) arrow.style.filter     = open ? '' : 'invert(1)';
    });
});
</script>
@endpush
@endsection
