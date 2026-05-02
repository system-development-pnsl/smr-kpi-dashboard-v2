@extends('layouts.app')
@section('title', 'Financial')
@section('page-title', 'Financial Dashboard')
@section('page-sub', 'Cash flow, bank balances & P&L — ' . now()->format('F Y'))

@section('content')
    <div style="display:flex;flex-direction:column;gap:8px;">

        {{-- Access badge --}}
        <div class="inline-flex items-center gap-2 px-3 py-2 bg-brand-black text-white rounded-lg text-[12px]">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span class="font-medium">Restricted Access</span>
            <span class="opacity-50">—</span>
            <span class="opacity-70">Owner / GM / AGM / Finance Director only</span>
        </div>

        {{-- Summary cards --}}
        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
            <button class="fin-toggle" data-target="sec-cashflow-kpi"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                    onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
                <div class="fin-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .15s;">
                    <svg class="fin-arrow" style="width:9px;height:9px;transition:transform .2s;transform:rotate(90deg);" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 3l14 9-14 9V3z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Cash Flow Overview</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Inflow, outflow &amp; net position</p>
                </div>
            </button>
            <div id="sec-cashflow-kpi" style="border-top:1px solid #f0f0ef;padding:16px 20px 20px;">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ([['label' => 'Total Inflow', 'value' => '$' . number_format($cashFlow['total_inflow'] / 1000, 0) . 'K', 'up' => true], ['label' => 'Total Outflow', 'value' => '$' . number_format($cashFlow['total_outflow'] / 1000, 0) . 'K', 'up' => false], ['label' => 'Net Cash', 'value' => '$' . number_format($cashFlow['net_position'] / 1000, 0) . 'K', 'up' => $cashFlow['net_position'] >= 0], ['label' => 'Total Reserves', 'value' => '$' . number_format($totalCash / 1000, 0) . 'K', 'up' => true]] as $card)
                        <div class="card">
                            <p class="text-[11px] font-medium text-brand-muted mb-1">{{ $card['label'] }}</p>
                            <p class="text-[22px] font-semibold text-brand-black tracking-tight">{{ $card['value'] }}</p>
                            <div class="flex items-center gap-1 mt-1">
                                <svg class="w-[11px] h-[11px] {{ $card['up'] ? 'text-status-green' : 'text-status-red' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $card['up'] ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}" />
                                </svg>
                                <span
                                    class="text-[10px] font-medium {{ $card['up'] ? 'text-status-green' : 'text-status-red' }}">
                                    {{ now()->format('F Y') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
            <button class="fin-toggle" data-target="sec-cashflow-detail"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                    onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
                <div class="fin-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .15s;">
                    <svg class="fin-arrow" style="width:9px;height:9px;transition:transform .2s;transform:rotate(90deg);" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 3l14 9-14 9V3z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Cash Flow &amp; Bank Accounts</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Statement &amp; bank reserves</p>
                </div>
            </button>
            <div id="sec-cashflow-detail" style="border-top:1px solid #f0f0ef;padding:16px 20px 20px;">
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
                        @foreach ($cashFlow['inflows'] as $row)
                            <div
                                class="flex items-center justify-between px-4 py-2 border-b border-brand-border/50 hover:bg-brand-bg transition-colors">
                                <div>
                                    <span class="text-[12px] text-brand-black">{{ $row->category }}</span>
                                    <span
                                        class="ml-2 text-[10px] text-brand-subtle font-mono">{{ $row->category_code }}</span>
                                </div>
                                <span
                                    class="text-[12px] font-medium text-status-green font-mono">+${{ number_format($row->amount) }}</span>
                            </div>
                        @endforeach
                        <div
                            class="flex items-center justify-between px-4 py-2.5 bg-status-green-bg/20 border-b border-brand-border">
                            <span class="text-[12px] font-bold text-brand-black">Total Inflow</span>
                            <span
                                class="text-[13px] font-bold text-status-green font-mono">+${{ number_format($cashFlow['total_inflow']) }}</span>
                        </div>

                        {{-- Outflows --}}
                        <div class="px-4 py-2 bg-status-red-bg/20 border-b border-brand-border">
                            <span class="text-[10px] font-bold text-status-red uppercase tracking-wide">Outflows</span>
                        </div>
                        @foreach ($cashFlow['outflows'] as $row)
                            <div
                                class="flex items-center justify-between px-4 py-2 border-b border-brand-border/50 hover:bg-brand-bg transition-colors">
                                <div>
                                    <span class="text-[12px] text-brand-black">{{ $row->category }}</span>
                                    <span
                                        class="ml-2 text-[10px] text-brand-subtle font-mono">{{ $row->category_code }}</span>
                                </div>
                                <span
                                    class="text-[12px] font-medium text-status-red font-mono">-${{ number_format($row->amount) }}</span>
                            </div>
                        @endforeach
                        <div
                            class="flex items-center justify-between px-4 py-2.5 bg-status-red-bg/20 border-b border-brand-border">
                            <span class="text-[12px] font-bold text-brand-black">Total Outflow</span>
                            <span
                                class="text-[13px] font-bold text-status-red font-mono">-${{ number_format($cashFlow['total_outflow']) }}</span>
                        </div>

                        {{-- Net --}}
                        <div class="flex items-center justify-between px-4 py-3 bg-brand-black text-white">
                            <span class="text-[13px] font-bold">Net Cash Position</span>
                            <span
                                class="text-[16px] font-bold font-mono {{ $cashFlow['net_position'] >= 0 ? 'text-status-green' : 'text-status-red' }}">
                                ${{ number_format($cashFlow['net_position']) }}
                            </span>
                        </div>
                    </div>

                    {{-- Bank Accounts --}}
                    <div class="card">
                        <h3 class="text-[13px] font-semibold text-brand-black mb-3">Bank Account Summary</h3>
                        <div class="space-y-3">
                            @foreach ($bankAccounts as $account)
                                @php
                                    $balance = $account->latestBalance?->closing_balance ?? 0;
                                    $ratio = $account->min_threshold > 0 ? $balance / $account->min_threshold : 2;
                                    $status = $ratio >= 2 ? 'green' : ($ratio >= 1 ? 'amber' : 'red');
                                    $barColors = [
                                        'green' => 'bg-status-green',
                                        'amber' => 'bg-status-amber',
                                        'red' => 'bg-status-red',
                                    ];
                                    $pct = min(($balance / max($account->min_threshold * 3, 1)) * 100, 100);
                                @endphp
                                <div class="pb-3 border-b border-brand-border last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div>
                                            <p class="text-[12px] font-medium text-brand-black">{{ $account->name }}</p>
                                            <p class="text-[10px] text-brand-subtle font-mono">{{ $account->code }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[13px] font-semibold font-mono text-brand-black">
                                                ${{ number_format($balance) }}</p>
                                            @if ($balance < $account->min_threshold)
                                                <p class="text-[9px] text-status-red font-medium">Below threshold</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-bar-fill {{ $barColors[$status] }}"
                                            style="width: {{ $pct }}%"></div>
                                    </div>
                                    <p class="text-[10px] text-brand-subtle mt-1">Min reserve:
                                        ${{ number_format($account->min_threshold) }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-brand-border">
                            <span class="text-[12px] font-semibold text-brand-black">Total</span>
                            <span
                                class="text-[16px] font-bold font-mono text-brand-black">${{ number_format($totalCash) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
            <button class="fin-toggle" data-target="sec-ar"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                    onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
                <div class="fin-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .15s;">
                    <svg class="fin-arrow" style="width:9px;height:9px;transition:transform .2s;transform:rotate(90deg);" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 3l14 9-14 9V3z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Account Receivable</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Unpaid invoices &amp; aging</p>
                </div>
            </button>
            <div id="sec-ar" style="border-top:1px solid #f0f0ef;padding:16px 20px 20px;">
                {{-- Account Receivable Dashboard --}}
                <div class="space-y-4">

                    {{-- AR KPI Cards --}}
                    <div class="card">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                            <div class="flex items-center gap-3 p-3 border border-brand-border rounded-lg">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-brand-bg rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-black" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-brand-muted leading-tight">Unpaid invoices amount</p>
                                    <p class="text-[22px] font-semibold text-brand-black tracking-tight leading-tight">
                                        55,237</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 border border-brand-border rounded-lg">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-brand-bg rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-black" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-brand-muted leading-tight">Overdue amount</p>
                                    <p class="text-[22px] font-semibold text-brand-black tracking-tight leading-tight">
                                        50,139</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 border border-brand-border rounded-lg">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-brand-bg rounded-lg flex items-center justify-center">
                                    <div class="relative">
                                        <svg class="w-5 h-5 text-brand-black" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span
                                            class="absolute -bottom-1 -right-2 text-[7px] font-bold text-brand-black">30</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[11px] text-brand-muted leading-tight">Overdue invoices 30+ days</p>
                                    <p class="text-[22px] font-semibold text-brand-black tracking-tight leading-tight">
                                        28,143</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 border border-brand-border rounded-lg">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-brand-bg rounded-lg flex items-center justify-center">
                                    <div class="relative">
                                        <svg class="w-5 h-5 text-brand-black" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span
                                            class="absolute -bottom-1 -right-2 text-[7px] font-bold text-brand-black">90</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[11px] text-brand-muted leading-tight">Overdue invoices 90+ days</p>
                                    <p class="text-[22px] font-semibold text-brand-black tracking-tight leading-tight">
                                        3,510</p>
                                </div>
                            </div>

                        </div>
                        <p class="text-[10px] text-brand-muted mt-3 italic">in home currency</p>
                    </div>

                    {{-- Unpaid invoices by customer (Top 10) --}}
                    <div class="card">
                        <div class="mb-5">
                            <span class="text-[13px] font-bold text-brand-black">Unpaid invoices amount by customer (Top
                                10)</span>
                            <span class="text-[11px] text-brand-muted ml-2">in home currency</span>
                        </div>
                        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-center">
                            <div class="xl:col-span-3 h-64">
                                <canvas id="arBarChart"></canvas>
                            </div>
                            <div class="xl:col-span-2 flex items-center gap-4 justify-center">
                                <div class="w-44 h-44 flex-shrink-0">
                                    <canvas id="arDonutChart"></canvas>
                                </div>
                                <div class="space-y-1.5" id="arLegend"></div>
                            </div>
                        </div>
                    </div>

                    {{-- AR Aging --}}
                    <div>
                        <h3 class="text-[15px] font-bold text-brand-black mb-3">AR Aging</h3>
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

                            {{-- Summary Table --}}
                            <div class="card p-0 overflow-hidden">
                                <div class="px-4 py-3 border-b border-brand-border flex items-center gap-2">
                                    <span class="text-[12px] font-semibold text-brand-black">Summary</span>
                                    <span class="text-[11px] text-brand-muted">in home currency</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-[11px]">
                                        <thead>
                                            <tr class="bg-brand-black text-white">
                                                <th class="text-left px-3 py-2.5 font-medium">Customer</th>
                                                <th class="text-right px-3 py-2.5 font-medium">Current</th>
                                                <th class="text-right px-3 py-2.5 font-medium">1-30 ▾</th>
                                                <th class="text-right px-3 py-2.5 font-medium">31-60</th>
                                                <th class="text-right px-3 py-2.5 font-medium">61-90</th>
                                                <th class="text-right px-3 py-2.5 font-medium whitespace-nowrap">91 and
                                                    over</th>
                                                <th class="text-right px-3 py-2.5 font-medium whitespace-nowrap">Amount due
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-brand-border">
                                            @foreach ([['name' => 'TechAdvantage Software', 'current' => 0, '1_30' => 8314, '31_60' => 0, '61_90' => 0, '91' => 0, 'total' => 8314], ['name' => 'Coastal Shipping', 'current' => 2450, '1_30' => 4234, '31_60' => 5350, '61_90' => 0, '91' => 2500, 'total' => 14534], ['name' => 'City Construction', 'current' => 0, '1_30' => 0, '31_60' => 5600, '61_90' => 3247, '91' => 1500, 'total' => 10347], ['name' => 'Urban Apparel', 'current' => 1200, '1_30' => 3800, '31_60' => 2100, '61_90' => 0, '91' => 0, 'total' => 7100], ['name' => 'Global Exports Co.', 'current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 3594, '91' => 1745, 'total' => 5339], ['name' => 'Green Gardens', 'current' => 1800, '1_30' => 1667, '31_60' => 0, '61_90' => 0, '91' => 0, 'total' => 3467], ['name' => 'Innovative Tech', 'current' => 0, '1_30' => 2012, '31_60' => 1400, '61_90' => 0, '91' => 0, 'total' => 3412], ['name' => 'Solar Solutions', 'current' => 1087, '1_30' => 1500, '31_60' => 0, '61_90' => 0, '91' => 0, 'total' => 2587]] as $row)
                                                <tr
                                                    class="{{ $loop->even ? 'bg-brand-bg/40' : 'bg-white' }} hover:bg-brand-bg transition-colors">
                                                    <td
                                                        class="px-3 py-2 text-brand-black font-medium max-w-[130px] truncate">
                                                        {{ $row['name'] }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['current'] ? number_format($row['current']) : '0' }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['1_30'] ? number_format($row['1_30']) : '0' }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['31_60'] ? number_format($row['31_60']) : '0' }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['61_90'] ? number_format($row['61_90']) : '0' }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['91'] ? number_format($row['91']) : '0' }}</td>
                                                    <td
                                                        class="px-3 py-2 text-right font-mono font-semibold text-brand-black">
                                                        {{ number_format($row['total']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Unpaid Invoices Table --}}
                            <div class="card p-0 overflow-hidden">
                                <div class="px-4 py-3 border-b border-brand-border flex items-center gap-2">
                                    <span class="text-[12px] font-semibold text-brand-black">Unpaid invoices</span>
                                    <span class="text-[11px] text-brand-muted">in home currency</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-[11px]">
                                        <thead>
                                            <tr class="bg-brand-black text-white">
                                                <th class="text-left px-3 py-2.5 font-medium">Customer</th>
                                                <th class="text-right px-3 py-2.5 font-medium">Number</th>
                                                <th class="text-right px-3 py-2.5 font-medium">Date</th>
                                                <th class="text-right px-3 py-2.5 font-medium">Due date</th>
                                                <th class="text-right px-3 py-2.5 font-medium whitespace-nowrap">Amount due
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-brand-border">
                                            @foreach ([['customer' => 'Global Exports Co.', 'number' => 1049, 'date' => 'Jul 30, 2024', 'due' => 'Aug 29, 2024', 'amount' => 3594], ['customer' => 'TechAdvantage Software', 'number' => 1055, 'date' => 'Aug 1, 2024', 'due' => 'Aug 31, 2024', 'amount' => 8314], ['customer' => 'Coastal Shipping', 'number' => 1023, 'date' => 'Jun 15, 2024', 'due' => 'Jul 15, 2024', 'amount' => 5350], ['customer' => 'City Construction', 'number' => 1031, 'date' => 'May 28, 2024', 'due' => 'Jun 27, 2024', 'amount' => 5600], ['customer' => 'Urban Apparel', 'number' => 1048, 'date' => 'Jul 25, 2024', 'due' => 'Aug 24, 2024', 'amount' => 3800], ['customer' => 'Innovative Tech', 'number' => 1037, 'date' => 'Jul 5, 2024', 'due' => 'Aug 4, 2024', 'amount' => 2012], ['customer' => 'Green Gardens', 'number' => 1042, 'date' => 'Jul 15, 2024', 'due' => 'Aug 14, 2024', 'amount' => 1667], ['customer' => 'Solar Solutions', 'number' => 1051, 'date' => 'Aug 5, 2024', 'due' => 'Sep 4, 2024', 'amount' => 1500]] as $inv)
                                                <tr
                                                    class="{{ $loop->even ? 'bg-brand-bg/40' : 'bg-white' }} hover:bg-brand-bg transition-colors">
                                                    <td class="px-3 py-2 text-brand-black font-medium">
                                                        {{ $inv['customer'] }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $inv['number'] }}</td>
                                                    <td class="px-3 py-2 text-right text-brand-subtle">{{ $inv['date'] }}
                                                    </td>
                                                    <td class="px-3 py-2 text-right text-brand-subtle">{{ $inv['due'] }}
                                                    </td>
                                                    <td
                                                        class="px-3 py-2 text-right font-mono font-semibold text-brand-black">
                                                        {{ number_format($inv['amount']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
            <button class="fin-toggle" data-target="sec-ap"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                    onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
                <div class="fin-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .15s;">
                    <svg class="fin-arrow" style="width:9px;height:9px;transition:transform .2s;transform:rotate(90deg);" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 3l14 9-14 9V3z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Account Payable</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Unpaid bills &amp; aging</p>
                </div>
            </button>
            <div id="sec-ap" style="border-top:1px solid #f0f0ef;padding:16px 20px 20px;">
                {{-- Account Payable Dashboard --}}
                <div class="space-y-4">

                    {{-- AP KPI Cards --}}
                    <div class="card">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                            <div class="flex items-center gap-3 p-3 border border-brand-border rounded-lg">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-brand-bg rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-black" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-brand-muted leading-tight">Amount Due</p>
                                    <p class="text-[22px] font-semibold text-brand-black tracking-tight leading-tight">
                                        19,217</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 border border-brand-border rounded-lg">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-brand-bg rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-black" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-brand-muted leading-tight">Overdue amount</p>
                                    <p class="text-[22px] font-semibold text-brand-black tracking-tight leading-tight">
                                        14,616</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 border border-brand-border rounded-lg">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-brand-bg rounded-lg flex items-center justify-center">
                                    <div class="relative">
                                        <svg class="w-5 h-5 text-brand-black" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span
                                            class="absolute -bottom-1 -right-2 text-[7px] font-bold text-brand-black">30</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[11px] text-brand-muted leading-tight">Overdue bills 30+ days</p>
                                    <p class="text-[22px] font-semibold text-brand-black tracking-tight leading-tight">
                                        4,817</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 border border-brand-border rounded-lg">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-brand-bg rounded-lg flex items-center justify-center">
                                    <div class="relative">
                                        <svg class="w-5 h-5 text-brand-black" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span
                                            class="absolute -bottom-1 -right-2 text-[7px] font-bold text-brand-black">90</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[11px] text-brand-muted leading-tight">Overdue bills 90+ days</p>
                                    <p class="text-[22px] font-semibold text-brand-black tracking-tight leading-tight">574
                                    </p>
                                </div>
                            </div>

                        </div>
                        <p class="text-[10px] text-brand-muted mt-3 italic">in home currency</p>
                    </div>

                    {{-- Unpaid bills by vendor (Top 10) --}}
                    <div class="card">
                        <div class="mb-5">
                            <span class="text-[13px] font-bold text-brand-black">Unpaid bills amount by vendor (Top
                                10)</span>
                            <span class="text-[11px] text-brand-muted ml-2">in home currency</span>
                        </div>
                        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-center">
                            <div class="xl:col-span-3 h-64">
                                <canvas id="apBarChart"></canvas>
                            </div>
                            <div class="xl:col-span-2 flex items-center gap-4 justify-center">
                                <div class="w-44 h-44 flex-shrink-0">
                                    <canvas id="apDonutChart"></canvas>
                                </div>
                                <div class="space-y-1.5" id="apLegend"></div>
                            </div>
                        </div>
                    </div>

                    {{-- AP Aging --}}
                    <div>
                        <h3 class="text-[15px] font-bold text-brand-black mb-3">AP Aging</h3>
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

                            {{-- Summary Table --}}
                            <div class="card p-0 overflow-hidden">
                                <div class="px-4 py-3 border-b border-brand-border flex items-center gap-2">
                                    <span class="text-[12px] font-semibold text-brand-black">Summary</span>
                                    <span class="text-[11px] text-brand-muted">in home currency</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-[11px]">
                                        <thead>
                                            <tr class="bg-brand-black text-white">
                                                <th class="text-left px-3 py-2.5 font-medium">Vendor</th>
                                                <th class="text-right px-3 py-2.5 font-medium">Current</th>
                                                <th class="text-right px-3 py-2.5 font-medium">1-30</th>
                                                <th class="text-right px-3 py-2.5 font-medium">31-60</th>
                                                <th class="text-right px-3 py-2.5 font-medium">61-90</th>
                                                <th class="text-right px-3 py-2.5 font-medium whitespace-nowrap">91 and
                                                    over</th>
                                                <th class="text-right px-3 py-2.5 font-medium whitespace-nowrap">Amount Due
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-brand-border">
                                            @foreach ([['name' => 'TechAdvantage Software', 'current' => 487, '1_30' => 3129, '31_60' => 0, '61_90' => 0, '91' => 0, 'total' => 3616], ['name' => 'Innovative Tech', 'current' => 0, '1_30' => 0, '31_60' => 3728, '61_90' => 0, '91' => 0, 'total' => 3728], ['name' => 'Coastal Shipping', 'current' => 1200, '1_30' => 2273, '31_60' => 0, '61_90' => 0, '91' => 0, 'total' => 3473], ['name' => 'Green Gardens', 'current' => 0, '1_30' => 1500, '31_60' => 1444, '61_90' => 0, '91' => 0, 'total' => 2944], ['name' => 'City Construction', 'current' => 487, '1_30' => 531, '31_60' => 1238, '61_90' => 0, '91' => 0, 'total' => 2256], ['name' => 'Urban Apparel', 'current' => 985, '1_30' => 1000, '31_60' => 0, '61_90' => 0, '91' => 0, 'total' => 1985], ['name' => 'Global Exports Co.', 'current' => 0, '1_30' => 0, '31_60' => 641, '61_90' => 0, '91' => 0, 'total' => 641], ['name' => 'Solar Solutions', 'current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 574, '91' => 0, 'total' => 574]] as $row)
                                                <tr
                                                    class="{{ $loop->even ? 'bg-brand-bg/40' : 'bg-white' }} hover:bg-brand-bg transition-colors">
                                                    <td
                                                        class="px-3 py-2 text-brand-black font-medium max-w-[130px] truncate">
                                                        {{ $row['name'] }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['current'] ? number_format($row['current']) : '0' }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['1_30'] ? number_format($row['1_30']) : '0' }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['31_60'] ? number_format($row['31_60']) : '0' }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['61_90'] ? number_format($row['61_90']) : '0' }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $row['91'] ? number_format($row['91']) : '0' }}</td>
                                                    <td
                                                        class="px-3 py-2 text-right font-mono font-semibold text-brand-black">
                                                        {{ number_format($row['total']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Unpaid Bills Table --}}
                            <div class="card p-0 overflow-hidden">
                                <div class="px-4 py-3 border-b border-brand-border flex items-center gap-2">
                                    <span class="text-[12px] font-semibold text-brand-black">Unpaid bills</span>
                                    <span class="text-[11px] text-brand-muted">in home currency</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-[11px]">
                                        <thead>
                                            <tr class="bg-brand-black text-white">
                                                <th class="text-left px-3 py-2.5 font-medium">Vendor</th>
                                                <th class="text-right px-3 py-2.5 font-medium">Bill No.</th>
                                                <th class="text-right px-3 py-2.5 font-medium">Bill date</th>
                                                <th class="text-right px-3 py-2.5 font-medium">Due date</th>
                                                <th class="text-right px-3 py-2.5 font-medium whitespace-nowrap">Past due
                                                    (days)</th>
                                                <th class="text-right px-3 py-2.5 font-medium whitespace-nowrap">Amount Due
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-brand-border">
                                            @foreach ([
            ['vendor' => 'City Construction', 'bill' => 2065, 'date' => 'Feb 1, 2025', 'due' => 'Mar 3, 2025', 'past' => 51, 'amount' => 1238],
            ['vendor' => 'Innovative Tech', 'bill' => 2071, 'date' => 'Mar 5, 2025', 'due' => 'Apr 4, 2025', 'past' => 27, 'amount' => 3728],
            ['vendor' => 'Green Gardens', 'bill' => 2068, 'date' => 'Feb 15, 2025', 'due' => 'Mar 17, 2025', 'past' => 45, 'amount' => 1444],
            ['vendor' => 'TechAdvantage Software', 'bill' => 2074, 'date' => 'Mar 12, 2025', 'due' => 'Apr 11, 2025', 'past' => 20, 'amount' => 3129],
            ['vendor' => 'Coastal Shipping', 'bill' => 2070, 'date' => 'Mar 1, 2025', 'due' => 'Mar 31, 2025', 'past' => 31, 'amount' => 2273],
            ['vendor' => 'Urban Apparel', 'bill' => 2075, 'date' => 'Mar 20, 2025', 'due' => 'Apr 19, 2025', 'past' => 12, 'amount' => 1000],
            ['vendor' => 'Global Exports Co.', 'bill' => 2062, 'date' => 'Jan 20, 2025', 'due' => 'Feb 19, 2025', 'past' => 71, 'amount' => 641],
            ['vendor' => 'Solar Solutions', 'bill' => 2058, 'date' => 'Jan 5, 2025', 'due' => 'Feb 4, 2025', 'past' => 86, 'amount' => 574],
        ] as $bill)
                                                <tr
                                                    class="{{ $loop->even ? 'bg-brand-bg/40' : 'bg-white' }} hover:bg-brand-bg transition-colors">
                                                    <td class="px-3 py-2 text-brand-black font-medium">
                                                        {{ $bill['vendor'] }}</td>
                                                    <td class="px-3 py-2 text-right font-mono text-brand-subtle">
                                                        {{ $bill['bill'] }}</td>
                                                    <td class="px-3 py-2 text-right text-brand-subtle">{{ $bill['date'] }}
                                                    </td>
                                                    <td class="px-3 py-2 text-right text-brand-subtle">{{ $bill['due'] }}
                                                    </td>
                                                    <td
                                                        class="px-3 py-2 text-right font-mono {{ $bill['past'] > 60 ? 'text-status-red font-semibold' : ($bill['past'] > 30 ? 'text-status-amber' : 'text-brand-subtle') }}">
                                                        {{ $bill['past'] }}</td>
                                                    <td
                                                        class="px-3 py-2 text-right font-mono font-semibold text-brand-black">
                                                        {{ number_format($bill['amount']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

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
                    if (wrap) wrap.style.background = open ? '#f3f3f2' : '#0a0a0a';
                    if (arrow) arrow.style.filter   = open ? '' : 'invert(1)';
                });
            });

            document.addEventListener('DOMContentLoaded', () => {
                const arCustomers = ['Coastal Shipping', 'City Construction', 'TechAdvantage Software', 'Urban Apparel',
                    'Global Exports Co.', 'Green Gardens', 'Innovative Tech', 'Solar Solutions'
                ];
                const arValues = [14534, 10347, 8314, 7100, 5339, 3467, 3412, 2587];
                const donutColors = ['#0A0A0A', '#262626', '#404040', '#525252', '#737373', '#8C8C8C', '#A3A3A3',
                    '#D4D4D4'
                ];

                new Chart(document.getElementById('arBarChart'), {
                    type: 'bar',
                    data: {
                        labels: arCustomers,
                        datasets: [{
                            data: arValues,
                            backgroundColor: '#DC2626',
                            borderRadius: 2,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: '#E5E5E3'
                                },
                                max: 16000,
                                ticks: {
                                    font: {
                                        family: 'DM Sans',
                                        size: 10
                                    },
                                    callback: v => (v / 1000).toFixed(0) + 'K'
                                },
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: 'DM Sans',
                                        size: 10
                                    }
                                }
                            },
                        },
                    },
                });

                new Chart(document.getElementById('arDonutChart'), {
                    type: 'doughnut',
                    data: {
                        labels: arCustomers,
                        datasets: [{
                            data: arValues,
                            backgroundColor: donutColors,
                            borderWidth: 1,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '58%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const total = arValues.reduce((a, b) => a + b, 0);
                                        return ` ${ctx.label}: ${((ctx.parsed / total) * 100).toFixed(1)}%`;
                                    }
                                }
                            }
                        },
                    },
                });

                const total = arValues.reduce((a, b) => a + b, 0);
                const legend = document.getElementById('arLegend');
                arCustomers.forEach((name, i) => {
                    const pct = ((arValues[i] / total) * 100).toFixed(1);
                    legend.innerHTML +=
                        `<div class="flex items-center gap-1.5"><span class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${donutColors[i]}"></span><span class="text-[10px] text-brand-muted">${name}</span></div>`;
                });

                // AP charts
                const apVendors = ['Innovative Tech', 'TechAdvantage Software', 'Coastal Shipping', 'Green Gardens',
                    'City Construction', 'Urban Apparel', 'Global Exports Co.', 'Solar Solutions'
                ];
                const apValues = [3728, 3616, 3473, 2944, 2256, 1985, 641, 574];

                new Chart(document.getElementById('apBarChart'), {
                    type: 'bar',
                    data: {
                        labels: apVendors,
                        datasets: [{
                            data: apValues,
                            backgroundColor: '#DC2626',
                            borderRadius: 2,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: '#E5E5E3'
                                },
                                max: 4000,
                                ticks: {
                                    font: {
                                        family: 'DM Sans',
                                        size: 10
                                    },
                                    callback: v => v.toLocaleString()
                                },
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: 'DM Sans',
                                        size: 10
                                    }
                                }
                            },
                        },
                    },
                });

                new Chart(document.getElementById('apDonutChart'), {
                    type: 'doughnut',
                    data: {
                        labels: apVendors,
                        datasets: [{
                            data: apValues,
                            backgroundColor: donutColors,
                            borderWidth: 1,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '58%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const t = apValues.reduce((a, b) => a + b, 0);
                                        return ` ${ctx.label}: ${((ctx.parsed / t) * 100).toFixed(1)}%`;
                                    }
                                }
                            }
                        },
                    },
                });

                const apLegend = document.getElementById('apLegend');
                const apTotal = apValues.reduce((a, b) => a + b, 0);
                apVendors.forEach((name, i) => {
                    apLegend.innerHTML +=
                        `<div class="flex items-center gap-1.5"><span class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${donutColors[i]}"></span><span class="text-[10px] text-brand-muted">${name}</span></div>`;
                });
            });
        </script>
    @endpush
@endsection
