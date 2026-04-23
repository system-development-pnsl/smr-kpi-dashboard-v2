@extends('layouts.app')
@section('title', 'Financial')
@section('page-title', 'Financial Dashboard')
@section('page-sub', 'Cash flow, bank balances & P&L — ' . now()->format('F Y'))

@section('content')
<div class="space-y-5">

    {{-- Access badge --}}
    <div class="inline-flex items-center gap-2 px-3 py-2 bg-brand-black text-white rounded-lg text-[12px]">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <span class="font-medium">Restricted Access</span>
        <span class="opacity-50">—</span>
        <span class="opacity-70">Owner / GM / AGM / Finance Director only</span>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'Total Inflow',   'value' => '$'.number_format($cashFlow['total_inflow']/1000,0).'K',  'up' => true],
            ['label' => 'Total Outflow',  'value' => '$'.number_format($cashFlow['total_outflow']/1000,0).'K', 'up' => false],
            ['label' => 'Net Cash',       'value' => '$'.number_format($cashFlow['net_position']/1000,0).'K',  'up' => $cashFlow['net_position'] >= 0],
            ['label' => 'Total Reserves', 'value' => '$'.number_format($totalCash/1000,0).'K',                 'up' => true],
        ] as $card)
            <div class="card">
                <p class="text-[11px] font-medium text-brand-muted mb-1">{{ $card['label'] }}</p>
                <p class="text-[22px] font-semibold text-brand-black tracking-tight">{{ $card['value'] }}</p>
                <div class="flex items-center gap-1 mt-1">
                    <svg class="w-[11px] h-[11px] {{ $card['up'] ? 'text-status-green' : 'text-status-red' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['up'] ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/>
                    </svg>
                    <span class="text-[10px] font-medium {{ $card['up'] ? 'text-status-green' : 'text-status-red' }}">
                        {{ now()->format('F Y') }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- Cash Flow Statement --}}
        <div class="xl:col-span-2 card overflow-hidden p-0">
            <div class="px-4 py-3 border-b border-brand-border flex items-center justify-between">
                <div>
                    <h3 class="text-[13px] font-semibold text-brand-black">Cash Flow Statement</h3>
                    <p class="text-[11px] text-brand-muted">{{ now()->format('F Y') }}</p>
                </div>
                <span class="badge-amber">Pending Approval</span>
            </div>

            {{-- Inflows --}}
            <div class="px-4 py-2 bg-status-green-bg/30 border-b border-brand-border">
                <span class="text-[10px] font-bold text-status-green uppercase tracking-wide">Inflows</span>
            </div>
            @foreach($cashFlow['inflows'] as $row)
                <div class="flex items-center justify-between px-4 py-2 border-b border-brand-border/50 hover:bg-brand-bg transition-colors">
                    <div>
                        <span class="text-[12px] text-brand-black">{{ $row->category }}</span>
                        <span class="ml-2 text-[10px] text-brand-subtle font-mono">{{ $row->category_code }}</span>
                    </div>
                    <span class="text-[12px] font-medium text-status-green font-mono">+${{ number_format($row->amount) }}</span>
                </div>
            @endforeach
            <div class="flex items-center justify-between px-4 py-2.5 bg-status-green-bg/20 border-b border-brand-border">
                <span class="text-[12px] font-bold text-brand-black">Total Inflow</span>
                <span class="text-[13px] font-bold text-status-green font-mono">+${{ number_format($cashFlow['total_inflow']) }}</span>
            </div>

            {{-- Outflows --}}
            <div class="px-4 py-2 bg-status-red-bg/20 border-b border-brand-border">
                <span class="text-[10px] font-bold text-status-red uppercase tracking-wide">Outflows</span>
            </div>
            @foreach($cashFlow['outflows'] as $row)
                <div class="flex items-center justify-between px-4 py-2 border-b border-brand-border/50 hover:bg-brand-bg transition-colors">
                    <div>
                        <span class="text-[12px] text-brand-black">{{ $row->category }}</span>
                        <span class="ml-2 text-[10px] text-brand-subtle font-mono">{{ $row->category_code }}</span>
                    </div>
                    <span class="text-[12px] font-medium text-status-red font-mono">-${{ number_format($row->amount) }}</span>
                </div>
            @endforeach
            <div class="flex items-center justify-between px-4 py-2.5 bg-status-red-bg/20 border-b border-brand-border">
                <span class="text-[12px] font-bold text-brand-black">Total Outflow</span>
                <span class="text-[13px] font-bold text-status-red font-mono">-${{ number_format($cashFlow['total_outflow']) }}</span>
            </div>

            {{-- Net --}}
            <div class="flex items-center justify-between px-4 py-3 bg-brand-black text-white">
                <span class="text-[13px] font-bold">Net Cash Position</span>
                <span class="text-[16px] font-bold font-mono {{ $cashFlow['net_position'] >= 0 ? 'text-status-green' : 'text-status-red' }}">
                    ${{ number_format($cashFlow['net_position']) }}
                </span>
            </div>
        </div>

        {{-- Bank Accounts --}}
        <div class="card">
            <h3 class="text-[13px] font-semibold text-brand-black mb-3">Bank Account Summary</h3>
            <div class="space-y-3">
                @foreach($bankAccounts as $account)
                    @php
                        $balance   = $account->latestBalance?->closing_balance ?? 0;
                        $ratio     = $account->min_threshold > 0 ? $balance / $account->min_threshold : 2;
                        $status    = $ratio >= 2 ? 'green' : ($ratio >= 1 ? 'amber' : 'red');
                        $barColors = ['green' => 'bg-status-green', 'amber' => 'bg-status-amber', 'red' => 'bg-status-red'];
                        $pct       = min(($balance / max($account->min_threshold * 3, 1)) * 100, 100);
                    @endphp
                    <div class="pb-3 border-b border-brand-border last:border-0 last:pb-0">
                        <div class="flex items-center justify-between mb-1.5">
                            <div>
                                <p class="text-[12px] font-medium text-brand-black">{{ $account->name }}</p>
                                <p class="text-[10px] text-brand-subtle font-mono">{{ $account->code }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[13px] font-semibold font-mono text-brand-black">${{ number_format($balance) }}</p>
                                @if($balance < $account->min_threshold)
                                    <p class="text-[9px] text-status-red font-medium">Below threshold</p>
                                @endif
                            </div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill {{ $barColors[$status] }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-[10px] text-brand-subtle mt-1">Min reserve: ${{ number_format($account->min_threshold) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-brand-border">
                <span class="text-[12px] font-semibold text-brand-black">Total</span>
                <span class="text-[16px] font-bold font-mono text-brand-black">${{ number_format($totalCash) }}</span>
            </div>
        </div>
    </div>

    {{-- Balance Trend Chart --}}
    <div class="card">
        <div class="mb-4">
            <h3 class="text-[13px] font-semibold text-brand-black">Balance Trend</h3>
            <p class="text-[11px] text-brand-muted">ABA Bank & ACLEDA Bank — 12-month view</p>
        </div>
        <div class="h-48">
            <canvas id="balanceTrendChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const labels   = @json($trendChart['labels'])
    const datasets = @json($trendChart['datasets'])

    new Chart(document.getElementById('balanceTrendChart'), {
        type: 'line',
        data: { labels, datasets: datasets.map((ds, i) => ({
            ...ds,
            borderColor:     i === 0 ? '#0A0A0A' : '#A3A3A3',
            borderWidth:     2,
            backgroundColor: 'transparent',
            borderDash:      i === 1 ? [4, 4] : [],
            tension:         0.3,
            pointRadius:     0,
        })) },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { font: { family: 'DM Sans', size: 11 } } } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: 'DM Sans', size: 10 } } },
                y: { grid: { color: '#E5E5E3' }, ticks: {
                    font: { family: 'DM Sans', size: 10 },
                    callback: v => '$' + (v / 1000).toFixed(0) + 'K'
                } },
            },
        },
    })
})
</script>
@endpush
@endsection
