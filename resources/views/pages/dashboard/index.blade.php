@extends('layouts.app')
@section('title', 'Overview')
@section('page-title', 'Operations Overview')
@section('page-sub', 'Hotel-wide performance at a glance — ' . now()->format('F Y'))

@section('content')
<div id="dash-container" style="display:flex;flex-direction:column;gap:20px;">

    {{-- ── Customize bar ────────────────────────────────────────── --}}
    <div style="display:flex;justify-content:flex-end;">
        <div style="position:relative;" id="dc-wrap">
            <button id="dc-btn" class="btn-secondary" style="font-size:11px;gap:6px;">
                <svg style="width:13px;height:13px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                Customize
            </button>
            <div id="dc-panel"
                 style="display:none;position:absolute;right:0;top:calc(100% + 6px);
                        width:232px;z-index:50;background:#fff;
                        border:1px solid #e5e5e3;border-radius:12px;
                        box-shadow:0 4px 20px rgba(0,0,0,.10);padding:8px;">
            </div>
        </div>
    </div>

    {{-- ── Row: At a Glance ─────────────────────────────────────── --}}
    <div id="row-stats" draggable="true" class="dash-row">
        <div class="flex items-center gap-2 mb-3">
            <div class="dash-grip" title="Drag to reorder"
                 style="cursor:grab;color:#d4d4d4;display:flex;align-items:center;flex-shrink:0;">
                <svg style="width:11px;height:15px;" fill="currentColor" viewBox="0 0 10 16">
                    <circle cx="2.5" cy="2"  r="1.3"/><circle cx="7.5" cy="2"  r="1.3"/>
                    <circle cx="2.5" cy="8"  r="1.3"/><circle cx="7.5" cy="8"  r="1.3"/>
                    <circle cx="2.5" cy="14" r="1.3"/><circle cx="7.5" cy="14" r="1.3"/>
                </svg>
            </div>
            <h2 class="text-[13px] font-semibold text-brand-black flex-1">At a Glance</h2>
            <button data-collapse-toggle="dash-stats"
                    class="cursor-pointer text-brand-muted hover:text-brand-black transition-colors p-1 rounded hover:bg-brand-bg">
                <svg data-chevron class="w-[14px] h-[14px] transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>
        <div id="dash-stats">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

            <div class="bg-brand-black text-white rounded-xl p-4 flex flex-col gap-1">
                <div class="w-7 h-7 bg-white/15 rounded-lg flex items-center justify-center mb-1">
                    <svg class="w-[14px] h-[14px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-[26px] font-semibold tracking-tight">${{ number_format($stats['total_cash'] / 1000, 0) }}K</p>
                <p class="text-[11px] text-white/70 font-medium">Total Cash Position</p>
                <p class="text-[10px] text-white/40">Across all bank accounts</p>
            </div>

            <div class="card">
                <div class="w-7 h-7 bg-brand-bg rounded-lg flex items-center justify-center mb-1">
                    <svg class="w-[14px] h-[14px] text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <p class="text-[26px] font-semibold text-brand-black">{{ $stats['open_tasks'] }}</p>
                <p class="text-[11px] text-brand-muted font-medium">Open Tasks</p>
                @if($stats['overdue_tasks'] > 0)
                    <p class="text-[10px] text-status-red font-medium">{{ $stats['overdue_tasks'] }} overdue</p>
                @else
                    <p class="text-[10px] text-brand-subtle">None overdue</p>
                @endif
            </div>

            <div class="card">
                <div class="w-7 h-7 bg-brand-bg rounded-lg flex items-center justify-center mb-1">
                    <svg class="w-[14px] h-[14px] text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <p class="text-[26px] font-semibold text-brand-black">15</p>
                <p class="text-[11px] text-brand-muted font-medium">Departments Active</p>
                <p class="text-[10px] text-brand-subtle">All HODs reporting</p>
            </div>

            <div class="card">
                <div class="w-7 h-7 bg-brand-bg rounded-lg flex items-center justify-center mb-1">
                    <svg class="w-[14px] h-[14px] text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-[26px] font-semibold text-brand-black">{{ $stats['unread_alerts'] }}</p>
                <p class="text-[11px] text-brand-muted font-medium">Unread Alerts</p>
                <p class="text-[10px] text-brand-subtle">Require attention</p>
            </div>
        </div>
        </div>{{-- end #dash-stats --}}
    </div>

    {{-- ── Row: Hotel-Wide KPIs ─────────────────────────────────── --}}
    <div id="row-kpi" draggable="true" class="dash-row">
        <div class="flex items-center gap-2 mb-3">
            <div class="dash-grip" title="Drag to reorder"
                 style="cursor:grab;color:#d4d4d4;display:flex;align-items:center;flex-shrink:0;">
                <svg style="width:11px;height:15px;" fill="currentColor" viewBox="0 0 10 16">
                    <circle cx="2.5" cy="2"  r="1.3"/><circle cx="7.5" cy="2"  r="1.3"/>
                    <circle cx="2.5" cy="8"  r="1.3"/><circle cx="7.5" cy="8"  r="1.3"/>
                    <circle cx="2.5" cy="14" r="1.3"/><circle cx="7.5" cy="14" r="1.3"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-[13px] font-semibold text-brand-black">Hotel-Wide KPIs</h2>
                <p class="text-[11px] text-brand-muted">{{ now()->format('F Y') }} — Executive Overview</p>
            </div>
            <a href="{{ route('kpi.index') }}" class="text-[11px] text-brand-muted hover:text-brand-black font-medium transition-colors flex-shrink-0">View All →</a>
            <button data-collapse-toggle="dash-kpis"
                    class="cursor-pointer text-brand-muted hover:text-brand-black transition-colors p-1 rounded hover:bg-brand-bg">
                <svg data-chevron class="w-[14px] h-[14px] transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>
        <div id="dash-kpis">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-3">
            @foreach($kpis as $kpi)
                @include('components.kpi-card', ['kpi' => $kpi])
            @endforeach
        </div>
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
        </div>{{-- end #dash-kpis --}}
    </div>

    {{-- ── Row: Revenue & Notifications ────────────────────────── --}}
    <div id="row-revenue" draggable="true" class="dash-row">
        <div class="flex items-center gap-2 mb-3">
            <div class="dash-grip" title="Drag to reorder"
                 style="cursor:grab;color:#d4d4d4;display:flex;align-items:center;flex-shrink:0;">
                <svg style="width:11px;height:15px;" fill="currentColor" viewBox="0 0 10 16">
                    <circle cx="2.5" cy="2"  r="1.3"/><circle cx="7.5" cy="2"  r="1.3"/>
                    <circle cx="2.5" cy="8"  r="1.3"/><circle cx="7.5" cy="8"  r="1.3"/>
                    <circle cx="2.5" cy="14" r="1.3"/><circle cx="7.5" cy="14" r="1.3"/>
                </svg>
            </div>
            <h2 class="text-[13px] font-semibold text-brand-black flex-1">Revenue & Notifications</h2>
            <button data-collapse-toggle="dash-revenue"
                    class="cursor-pointer text-brand-muted hover:text-brand-black transition-colors p-1 rounded hover:bg-brand-bg">
                <svg data-chevron class="w-[14px] h-[14px] transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>
        <div id="dash-revenue">
        <div id="grid-revenue" class="grid grid-cols-1 xl:grid-cols-3 gap-4">

            <div id="panel-revenue-chart" class="xl:col-span-2 card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-[13px] font-semibold text-brand-black">Revenue by Source</h3>
                        <p class="text-[11px] text-brand-muted">12-month trend</p>
                    </div>
                    <div class="flex items-center gap-1">
                        @foreach(['6M', '12M', 'YTD'] as $p)
                            <button onclick="filterRevenue('{{ $p }}')"
                                    class="text-[10px] font-medium px-2.5 py-1 rounded-md transition-colors
                                           {{ $p === '12M' ? 'bg-brand-black text-white' : 'text-brand-muted hover:bg-brand-bg' }}">
                                {{ $p }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="h-52"><canvas id="revenueChart"></canvas></div>
            </div>

            <div id="panel-notif" class="card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-[13px] font-semibold text-brand-black">Alerts & Notifications</h3>
                    @if($unreadCount > 0)
                        <span class="text-[10px] bg-brand-black text-white px-2 py-0.5 rounded-full font-semibold">
                            {{ $unreadCount }} new
                        </span>
                    @endif
                </div>
                <div class="space-y-1">
                    @foreach($notifications->take(5) as $notification)
                        @include('components.notification-item', ['notification' => $notification])
                    @endforeach
                </div>
                <a href="{{ route('notifications.index') }}"
                   class="block mt-3 text-center text-[11px] text-brand-muted hover:text-brand-black transition-colors font-medium py-1.5 border border-brand-border rounded-lg hover:bg-brand-bg">
                    View all notifications
                </a>
            </div>
        </div>
        </div>{{-- end #dash-revenue --}}
    </div>

    {{-- ── Row: Tasks & Departments ─────────────────────────────── --}}
    <div id="row-tasks" draggable="true" class="dash-row">
        <div class="flex items-center gap-2 mb-3">
            <div class="dash-grip" title="Drag to reorder"
                 style="cursor:grab;color:#d4d4d4;display:flex;align-items:center;flex-shrink:0;">
                <svg style="width:11px;height:15px;" fill="currentColor" viewBox="0 0 10 16">
                    <circle cx="2.5" cy="2"  r="1.3"/><circle cx="7.5" cy="2"  r="1.3"/>
                    <circle cx="2.5" cy="8"  r="1.3"/><circle cx="7.5" cy="8"  r="1.3"/>
                    <circle cx="2.5" cy="14" r="1.3"/><circle cx="7.5" cy="14" r="1.3"/>
                </svg>
            </div>
            <h2 class="text-[13px] font-semibold text-brand-black flex-1">Tasks & Departments</h2>
            <a href="{{ route('tasks.index') }}" class="text-[11px] text-brand-muted hover:text-brand-black font-medium transition-colors flex-shrink-0">View All →</a>
            <button data-collapse-toggle="dash-tasks"
                    class="cursor-pointer text-brand-muted hover:text-brand-black transition-colors p-1 rounded hover:bg-brand-bg">
                <svg data-chevron class="w-[14px] h-[14px] transition-transform duration-200"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>
        </div>
        <div id="dash-tasks">
        <div id="grid-tasks" class="grid grid-cols-1 xl:grid-cols-3 gap-4">

            <div id="panel-recent-tasks" class="xl:col-span-2 card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-[13px] font-semibold text-brand-black">Recent Tasks</h3>
                </div>
                <div class="flex gap-1 mb-3 border-b border-brand-border pb-2 overflow-x-auto scrollbar-none">
                    @foreach(['all' => 'All', 'IN_PROGRESS' => 'In Progress', 'BLOCKED' => 'Blocked', 'TODO' => 'To Do', 'DONE' => 'Done'] as $key => $label)
                        <a href="{{ route('dashboard', ['status' => $key]) }}"
                           class="text-[11px] font-medium px-2.5 py-1 rounded-md transition-colors whitespace-nowrap
                                  {{ request('status', 'all') === $key ? 'bg-brand-black text-white' : 'text-brand-muted hover:bg-brand-bg' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                <div class="space-y-0.5">
                    @forelse($recentTasks as $task)
                        @include('components.task-row', ['task' => $task])
                    @empty
                        <div class="text-center py-8 text-brand-muted text-[12px]">
                            <svg class="w-6 h-6 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Great work — no tasks in this status!
                        </div>
                    @endforelse
                </div>
            </div>

            <div id="panel-dept" class="card space-y-5">
                <div>
                    <h3 class="text-[13px] font-semibold text-brand-black mb-3">Tasks by Department</h3>
                    <div class="space-y-2.5">
                        @foreach($tasksByDept as $dept)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-medium text-brand-black">{{ $dept->label }}</span>
                                        @if($dept->overdue_count > 0)
                                            <span class="badge-red">{{ $dept->overdue_count }} overdue</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-brand-muted">{{ $dept->done_count }}/{{ $dept->total_count }}</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-bar-fill bg-brand-black"
                                         style="width: {{ $dept->total_count > 0 ? ($dept->done_count / $dept->total_count) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="pt-4 border-t border-brand-border">
                    <h4 class="text-[11px] font-semibold text-brand-black mb-2.5">Bank Balances</h4>
                    <div class="space-y-2">
                        @foreach($bankAccounts as $account)
                            @php
                                $ratio  = $account->latestBalance?->closing_balance / max($account->min_threshold, 1);
                                $status = $ratio >= 2 ? 'green' : ($ratio >= 1 ? 'amber' : 'red');
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="dot-{{ $status }}"></div>
                                    <span class="text-[11px] text-brand-black">{{ $account->name }}</span>
                                </div>
                                <span class="text-[11px] font-medium font-mono text-brand-black">
                                    ${{ number_format(($account->latestBalance?->closing_balance ?? 0) / 1000, 0) }}K
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-brand-border">
                        <span class="text-[11px] font-semibold text-brand-black">Total</span>
                        <span class="text-[13px] font-bold font-mono text-brand-black">
                            ${{ number_format($stats['total_cash'] / 1000, 0) }}K
                        </span>
                    </div>
                </div>
            </div>
        </div>
        </div>{{-- end #dash-tasks --}}
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    initRevenueChart('revenueChart',
        @json($revenueChart['labels']),
        @json($revenueChart['datasets'])
    )

    // ─────────────────────────────────────────────────────────────
    // Widget catalog
    // ─────────────────────────────────────────────────────────────
    const CATALOG = [
        { id: 'row-stats',   label: 'At a Glance',            panels: [] },
        { id: 'row-kpi',     label: 'Hotel-Wide KPIs',         panels: [] },
        { id: 'row-revenue', label: 'Revenue & Notifications', panels: [
            { id: 'panel-revenue-chart', label: 'Revenue Chart' },
            { id: 'panel-notif',         label: 'Notifications Feed' },
        ]},
        { id: 'row-tasks', label: 'Tasks & Departments', panels: [
            { id: 'panel-recent-tasks', label: 'Recent Tasks' },
            { id: 'panel-dept',         label: 'Dept & Bank Balances' },
        ]},
    ]

    const LS_KEY = 'dash_widgets_v3'

    function loadState() {
        try {
            const s = JSON.parse(localStorage.getItem(LS_KEY))
            if (s && Array.isArray(s.order) && Array.isArray(s.hidden)) return s
        } catch {}
        return { order: CATALOG.map(r => r.id), hidden: [] }
    }

    function saveState(s) {
        localStorage.setItem(LS_KEY, JSON.stringify(s))
    }

    // Read current DOM order of rows
    function domOrder() {
        return [...document.getElementById('dash-container').children]
            .filter(el => el.classList.contains('dash-row'))
            .map(el => el.id)
    }

    // ── Apply state ───────────────────────────────────────────────
    function applyState(s) {
        const container = document.getElementById('dash-container')

        // Reorder DOM (appendChild moves to end in sequence; customize bar stays first)
        s.order.forEach(id => {
            const el = document.getElementById(id)
            if (el) container.appendChild(el)
        })

        // Row + panel visibility
        CATALOG.forEach(row => {
            const rowEl = document.getElementById(row.id)
            if (rowEl) rowEl.style.display = s.hidden.includes(row.id) ? 'none' : ''
            row.panels.forEach(p => {
                const pEl = document.getElementById(p.id)
                if (pEl) pEl.style.display = s.hidden.includes(p.id) ? 'none' : ''
            })
        })

        // Expand lone panel when its partner is hidden
        adjustPair('panel-revenue-chart', 'panel-notif')
        adjustPair('panel-recent-tasks',  'panel-dept')

        renderPanel(s)
    }

    function adjustPair(idA, idB) {
        const a = document.getElementById(idA)
        const b = document.getElementById(idB)
        if (!a || !b) return
        const aHidden = a.style.display === 'none'
        const bHidden = b.style.display === 'none'
        a.style.gridColumn = (!aHidden && bHidden)  ? '1 / -1' : ''
        b.style.gridColumn = (aHidden  && !bHidden) ? '1 / -1' : ''
    }

    // ── Customize panel ───────────────────────────────────────────
    function pill(on) {
        return `<div style="position:relative;width:28px;height:16px;border-radius:999px;
                             background:${on ? '#0a0a0a' : '#d4d4d4'};flex-shrink:0;
                             pointer-events:none;transition:background .2s;">
                    <span style="position:absolute;top:2px;left:2px;width:12px;height:12px;
                                 background:#fff;border-radius:50%;box-shadow:0 1px 2px rgba(0,0,0,.2);
                                 transition:transform .2s;
                                 transform:translateX(${on ? '12px' : '0px'});"></span>
                </div>`
    }

    function renderPanel(s) {
        const panel = document.getElementById('dc-panel')
        if (!panel) return

        let html = `<p style="font-size:9px;font-weight:600;letter-spacing:.08em;
                               text-transform:uppercase;color:#a3a3a3;padding:4px 8px 8px;">
                        Visible Sections
                    </p>`

        s.order.forEach(rowId => {
            const row   = CATALOG.find(r => r.id === rowId)
            if (!row) return
            const rowOn = !s.hidden.includes(rowId)

            html += `
            <div class="dc-row-item" data-row="${rowId}"
                 style="display:flex;align-items:center;gap:8px;padding:6px 8px;
                        border-radius:8px;cursor:pointer;"
                 onmouseover="this.style.background='#f5f5f3'"
                 onmouseout="this.style.background='transparent'">
                ${pill(rowOn)}
                <span style="font-size:12px;font-weight:500;color:#0a0a0a;
                             user-select:none;flex:1;opacity:${rowOn ? 1 : .4};">
                    ${row.label}
                </span>
            </div>`

            row.panels.forEach(p => {
                const pOn = !s.hidden.includes(p.id)
                html += `
                <div class="dc-panel-item" data-panel="${p.id}"
                     style="display:flex;align-items:center;gap:8px;padding:5px 8px 5px 36px;
                            border-radius:8px;cursor:pointer;"
                     onmouseover="this.style.background='#f5f5f3'"
                     onmouseout="this.style.background='transparent'">
                    ${pill(pOn)}
                    <span style="font-size:11px;color:${pOn ? '#404040' : '#c0c0c0'};user-select:none;">
                        ${p.label}
                    </span>
                </div>`
            })
        })

        const hiddenCount = s.hidden.length
        html += `
        <div style="border-top:1px solid #e5e5e3;margin-top:6px;padding:8px 9px 3px;
                    display:flex;align-items:center;justify-content:space-between;">
            <button id="dc-reset"
                    style="font-size:11px;color:#a3a3a3;background:none;border:none;
                           cursor:pointer;padding:0;font-family:inherit;"
                    onmouseover="this.style.color='#0a0a0a'"
                    onmouseout="this.style.color='#a3a3a3'">Reset layout</button>
            <span style="font-size:10px;color:#d4d4d4;">
                ${hiddenCount > 0 ? hiddenCount + ' hidden' : 'All visible'}
            </span>
        </div>`

        panel.innerHTML = html
        bindPanelEvents()
    }

    function bindPanelEvents() {
        document.querySelectorAll('.dc-row-item').forEach(el => {
            el.addEventListener('click', () => {
                const s   = loadState()
                const id  = el.dataset.row
                const idx = s.hidden.indexOf(id)
                if (idx === -1) s.hidden.push(id)
                else            s.hidden.splice(idx, 1)
                saveState(s)
                applyState(s)
            })
        })

        document.querySelectorAll('.dc-panel-item').forEach(el => {
            el.addEventListener('click', () => {
                const s   = loadState()
                const id  = el.dataset.panel
                const idx = s.hidden.indexOf(id)
                if (idx === -1) s.hidden.push(id)
                else            s.hidden.splice(idx, 1)
                saveState(s)
                applyState(s)
            })
        })

        const resetBtn = document.getElementById('dc-reset')
        if (resetBtn) {
            resetBtn.addEventListener('click', e => {
                e.stopPropagation()
                localStorage.removeItem(LS_KEY)
                applyState(loadState())
            })
        }
    }

    // ── Drag-and-drop ─────────────────────────────────────────────
    const container = document.getElementById('dash-container')

    // Drop indicator line
    const dropLine = document.createElement('div')
    dropLine.style.cssText = `height:3px;background:#2563eb;border-radius:2px;
                               margin:2px 0;display:none;pointer-events:none;flex-shrink:0;`
    container.appendChild(dropLine)

    let dragging = null

    document.querySelectorAll('.dash-row').forEach(row => {

        row.addEventListener('dragstart', e => {
            dragging = row
            e.dataTransfer.effectAllowed = 'move'
            e.dataTransfer.setData('text/plain', row.id)
            // Defer opacity so the ghost image captures the full row
            setTimeout(() => {
                row.style.opacity  = '0.4'
                row.style.outline  = '2px dashed #e5e5e3'
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

            // Insert dragged row at drop line position, then remove line
            container.insertBefore(dragging, dropLine)
            dropLine.style.display = 'none'

            // Persist new order
            const s   = loadState()
            s.order   = domOrder()
            saveState(s)
            renderPanel(s) // reflect new order in panel
        })
    })

    // Hide drop line if mouse leaves container
    container.addEventListener('dragleave', e => {
        if (!container.contains(e.relatedTarget)) {
            dropLine.style.display = 'none'
        }
    })

    // Prevent drag firing on interactive children (inputs, buttons, links)
    container.addEventListener('dragstart', e => {
        if (!e.target.classList.contains('dash-row') && !e.target.closest('.dash-grip')) {
            if (!e.target.closest('.dash-row') || e.target.tagName === 'A' ||
                e.target.tagName === 'BUTTON' || e.target.tagName === 'INPUT' ||
                e.target.tagName === 'CANVAS') {
                e.preventDefault()
            }
        }
    }, true)

    // ── Customize button open / close ─────────────────────────────
    const dcWrap  = document.getElementById('dc-wrap')
    const dcBtn   = document.getElementById('dc-btn')
    const dcPanel = document.getElementById('dc-panel')

    dcBtn.addEventListener('click', e => {
        e.stopPropagation()
        dcPanel.style.display = dcPanel.style.display === 'none' ? 'block' : 'none'
    })
    document.addEventListener('click', e => {
        if (!dcWrap.contains(e.target)) dcPanel.style.display = 'none'
    })

    // ── Boot ──────────────────────────────────────────────────────
    applyState(loadState())
})
</script>
@endpush
@endsection
