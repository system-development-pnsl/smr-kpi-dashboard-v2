@extends('layouts.app')
@section('title', 'Overview')
@section('page-title', 'Operations Overview')
@section('page-sub', 'Hotel-wide performance at a glance — ' . now()->format('F Y'))

@section('content')
<div class="space-y-6">

    {{-- ── Top stat strip ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

        {{-- Total Cash --}}
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

        {{-- Open Tasks --}}
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

        {{-- Departments --}}
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

        {{-- Alerts --}}
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

    {{-- ── KPI Grid ─────────────────────────────────────────────── --}}
    <section>
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-[13px] font-semibold text-brand-black">Hotel-Wide KPIs</h2>
                <p class="text-[11px] text-brand-muted">{{ now()->format('F Y') }} — Executive Overview</p>
            </div>
            <a href="{{ route('kpi.index') }}" class="text-[11px] text-brand-muted hover:text-brand-black font-medium transition-colors">View All →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-3">
            @foreach($kpis as $kpi)
                @include('components.kpi-card', ['kpi' => $kpi])
            @endforeach
        </div>
    </section>

    {{-- ── Revenue Chart + Notifications ──────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- Revenue chart --}}
        <div class="xl:col-span-2 card">
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
            <div class="h-52">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="card">
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

    {{-- ── Tasks + Dept Summary ─────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- Recent tasks --}}
        <div class="xl:col-span-2 card">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-[13px] font-semibold text-brand-black">Recent Tasks</h3>
                <a href="{{ route('tasks.index') }}" class="text-[11px] text-brand-muted hover:text-brand-black font-medium">View all →</a>
            </div>

            {{-- Filter tabs --}}
            <div class="flex gap-1 mb-3 border-b border-brand-border pb-2 overflow-x-auto scrollbar-none">
                @foreach(['all' => 'All', 'IN_PROGRESS' => 'In Progress', 'BLOCKED' => 'Blocked', 'TODO' => 'To Do', 'DONE' => 'Done'] as $key => $label)
                    <a href="{{ route('dashboard', ['status' => $key]) }}"
                       class="text-[11px] font-medium px-2.5 py-1 rounded-md transition-colors
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

        {{-- Dept summary + Bank balances --}}
        <div class="card space-y-5">
            {{-- Tasks by dept --}}
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

            {{-- Bank balances --}}
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
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initRevenueChart('revenueChart',
            @json($revenueChart['labels']),
            @json($revenueChart['datasets'])
        )
    })
</script>
@endpush
@endsection
