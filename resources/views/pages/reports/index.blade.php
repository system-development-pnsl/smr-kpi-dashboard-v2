@extends('layouts.app')
@section('title', 'Operations')
@section('page-title', 'Operations')
@section('page-sub', 'Chairman\'s Daily Briefing')

@section('content')
@php
    $from = request('from') ? \Carbon\Carbon::parse(request('from')) : now()->subDay();
    $to   = request('to')   ? \Carbon\Carbon::parse(request('to'))   : now();
@endphp

<div>

    {{-- ── Header card ─────────────────────────────────────────────── --}}
    <div style="background:#fff;border:1px solid #e5e5e3;border-radius:16px;padding:20px 24px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div>
                <p style="font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;
                           color:#a3a3a3;margin:0 0 4px;">Chairman's Daily Briefing</p>
                <p style="font-size:22px;font-weight:700;color:#0a0a0a;margin:0;letter-spacing:-.3px;">
                    {{ $to->format('l, d F Y') }}
                </p>
                <p style="font-size:11px;color:#a3a3a3;margin:6px 0 0;">
                    Sun &amp; Moon Riverside Hotel &nbsp;·&nbsp; Prepared at 07:00
                </p>
            </div>

            {{-- Custom date range picker --}}
            <form method="GET" action="{{ route('reports.index') }}" id="range-form">
                <input type="hidden" name="from" id="drp-from-val" value="{{ $from->format('Y-m-d') }}">
                <input type="hidden" name="to"   id="drp-to-val"   value="{{ $to->format('Y-m-d') }}">

                <div style="position:relative;">
                    {{-- Trigger button --}}
                    <button type="button" id="drp-trigger"
                            style="display:flex;align-items:center;gap:10px;
                                   background:#f9f9f8;border:1px solid #e5e5e3;
                                   border-radius:12px;padding:10px 16px;cursor:pointer;
                                   color:#0a0a0a;transition:background .15s;"
                            onmouseover="this.style.background='#f3f3f2'"
                            onmouseout="this.style.background='#f9f9f8'">
                        <svg style="width:14px;height:14px;color:#a3a3a3;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="text-align:left;">
                                <div style="font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                                            color:#a3a3a3;margin-bottom:2px;">From</div>
                                <div id="drp-from-label" style="font-size:13px;font-weight:600;color:#0a0a0a;white-space:nowrap;">
                                    {{ $from->format('M d, Y') }}
                                </div>
                            </div>
                            <svg style="width:14px;height:14px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                            <div style="text-align:left;">
                                <div style="font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                                            color:#a3a3a3;margin-bottom:2px;">To</div>
                                <div id="drp-to-label" style="font-size:13px;font-weight:600;color:#0a0a0a;white-space:nowrap;">
                                    {{ $to->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                        <svg style="width:13px;height:13px;color:#a3a3a3;margin-left:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Calendar dropdown --}}
                    <div id="drp-dropdown"
                         style="display:none;position:absolute;right:0;top:calc(100% + 8px);z-index:200;
                                background:#fff;border-radius:16px;
                                box-shadow:0 8px 40px rgba(0,0,0,.18),0 2px 8px rgba(0,0,0,.08);
                                padding:0;overflow:hidden;min-width:620px;">

                        {{-- Two calendars side by side --}}
                        <div style="display:flex;gap:0;">
                            <div id="drp-cal-left"  style="flex:1;padding:20px 16px 0;"></div>
                            <div style="width:1px;background:#f0f0ef;margin:20px 0;"></div>
                            <div id="drp-cal-right" style="flex:1;padding:20px 16px 0;"></div>
                        </div>

                        {{-- Footer --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;
                                    padding:12px 20px;border-top:1px solid #f0f0ef;margin-top:12px;">
                            <div style="display:flex;gap:6px;">
                                @foreach([
                                    ['today',  'Today',       0, 0],
                                    ['week',   'Last 7 days', 6, 0],
                                    ['month',  'This month',  null, null],
                                    ['30days', 'Last 30 days',29, 0],
                                ] as [$key,$label,$a,$b])
                                <button type="button" class="drp-preset" data-preset="{{ $key }}"
                                        style="font-size:11px;font-weight:600;padding:5px 11px;border-radius:100px;
                                               border:1px solid #e5e5e3;background:#fff;cursor:pointer;
                                               color:#737373;transition:all .15s;"
                                        onmouseover="this.style.background='#0a0a0a';this.style.color='#fff';this.style.borderColor='#0a0a0a';"
                                        onmouseout="this.style.background='#fff';this.style.color='#737373';this.style.borderColor='#e5e5e3';">
                                    {{ $label }}
                                </button>
                                @endforeach
                            </div>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <span id="drp-range-label" style="font-size:11px;color:#a3a3a3;"></span>
                                <button type="button" id="drp-cancel"
                                        style="font-size:12px;font-weight:600;padding:7px 16px;border-radius:8px;
                                               border:1px solid #e5e5e3;background:#fff;cursor:pointer;color:#737373;">
                                    Cancel
                                </button>
                                <button type="button" id="drp-apply"
                                        style="font-size:12px;font-weight:700;padding:7px 20px;border-radius:8px;
                                               border:none;background:#0a0a0a;cursor:pointer;color:#fff;">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Quick stats strip --}}
        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:16px;">
            @foreach([
                ['Occupancy',    '84%',   '↑6% vs last week',  true],
                ['Revenue MTD',  '$91K',  '103% of target',     true],
                ['Events Today', '3',     '$48.7K revenue',     true],
                ['Staff On Duty','88',    '3 called out',       false],
            ] as [$l, $v, $s, $pos])
            <div style="background:#f9f9f8;border:1px solid #f0f0ef;border-radius:12px;padding:14px 16px;">
                <p style="font-size:9px;text-transform:uppercase;letter-spacing:.07em;
                           color:#a3a3a3;font-weight:600;margin:0 0 5px;">{{ $l }}</p>
                <p style="font-size:22px;font-weight:700;color:#0a0a0a;margin:0;line-height:1;">{{ $v }}</p>
                <p style="font-size:10px;font-weight:600;margin:5px 0 0;color:{{ $pos ? '#16a34a' : '#dc2626' }};">{{ $s }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Accordion sections ───────────────────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:8px;">

        {{-- ── 1. Hotel Snapshot ───────────────────────────────────── --}}
        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
            <button class="ops-toggle" data-target="ops-hotel"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;
                           background:transparent;border:none;cursor:pointer;text-align:left;
                           transition:background .15s;"
                    onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
                <div class="ops-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;
                                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;
                                                    transition:background .15s;">
                    <svg class="ops-arrow" style="width:9px;height:9px;transition:transform .2s;"
                         fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 3l14 9-14 9V3z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Hotel Snapshot</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Occupancy, ADR, RevPAR, Revenue</p>
                </div>
                <span style="font-size:11px;font-weight:600;color:#a3a3a3;flex-shrink:0;">84% occ · $91K MTD</span>
            </button>
            <div id="ops-hotel" class="ops-body" style="display:none;border-top:1px solid #f0f0ef;">
                <div style="padding:16px 20px;">
                    <div style="display:flex;flex-wrap:wrap;gap:10px;">
                        @foreach([
                            ['hotel-occ',     'Occupancy',           '84%',     '▲ 6% vs last Sat',    true],
                            ['hotel-adr',     'ADR',                 '$224',    '▲ $18 vs last week',  true],
                            ['hotel-revpar',  'RevPAR',              '$188',    '▲ 8% vs last week',   true],
                            ['hotel-rev',     'Revenue (MTD)',       '$91K',    '103% of target',      true],
                            ['hotel-rooms',   'Rooms Sold',          '168',     'of 200 total',        true],
                            ['hotel-ebitda',  'EBITDA Margin',       '22%',     '▲ 2pp vs budget',     true],
                            ['hotel-los',     'Avg. Length of Stay', '2.8 nts', '▲ 0.3 vs last week',  true],
                            ['hotel-cancel',  'Cancellation Rate',   '4.2%',    '▼ 1.1% vs last week', true],
                            ['hotel-nps',     'Guest NPS',           '74',      '▲ 3 pts vs last mo',  true],
                        ] as [$id, $label, $val, $change, $pos])
                        <div class="ops-metric-card" data-metric-id="{{ $id }}"
                             style="flex:1;min-width:160px;max-width:220px;background:#f9f9f8;
                                    border-radius:12px;padding:14px;position:relative;
                                    border:1px solid #f0f0ef;transition:opacity .2s;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                                <p style="font-size:9px;text-transform:uppercase;letter-spacing:.07em;
                                           color:#a3a3a3;font-weight:600;margin:0;">{{ $label }}</p>
                                <button class="ops-metric-toggle" data-metric-id="{{ $id }}"
                                        type="button"
                                        style="width:34px;height:19px;border-radius:100px;border:none;
                                               cursor:pointer;padding:0;position:relative;flex-shrink:0;
                                               background:#10b981;transition:background .2s;"
                                        title="Toggle metric visibility">
                                    <span style="position:absolute;top:2px;left:17px;width:15px;height:15px;
                                                 border-radius:50%;background:#fff;
                                                 box-shadow:0 1px 3px rgba(0,0,0,.2);
                                                 transition:left .2s;display:block;"></span>
                                </button>
                            </div>
                            <p style="font-size:24px;font-weight:700;color:#0a0a0a;margin:0;line-height:1;
                                       letter-spacing:-.5px;">{{ $val }}</p>
                            <p style="font-size:10px;font-weight:600;margin:8px 0 0;
                                       color:{{ $pos ? '#16a34a' : '#dc2626' }};">{{ $change }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. Arrivals, Departures & VIP List ──────────────────── --}}
        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
            <button class="ops-toggle" data-target="ops-arrivals"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;
                           background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                    onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
                <div class="ops-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;
                                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg class="ops-arrow" style="width:9px;height:9px;transition:transform .2s;"
                         fill="currentColor" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Arrivals, Departures &amp; VIP List</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Guest movements and VIP protocols</p>
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0;">
                    <span style="font-size:9px;font-weight:700;padding:3px 8px;border-radius:100px;
                                 background:#dcfce7;color:#16a34a;">12 arrivals</span>
                    <span style="font-size:9px;font-weight:700;padding:3px 8px;border-radius:100px;
                                 background:#fee2e2;color:#dc2626;">9 departures</span>
                </div>
            </button>
            <div id="ops-arrivals" class="ops-body" style="display:none;border-top:1px solid #f0f0ef;">
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:16px;">

                    <div>
                        <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
                                   color:#16a34a;margin:0 0 10px;display:flex;align-items:center;gap:6px;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
                            Arrivals (12)
                        </p>
                        <table style="width:100%;border-collapse:collapse;font-size:11px;">
                            <thead>
                                <tr style="color:#a3a3a3;font-size:10px;text-transform:uppercase;letter-spacing:.06em;">
                                    <th style="text-align:left;padding:6px 12px 6px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Guest Name</th>
                                    <th style="text-align:left;padding:6px 12px 6px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Room</th>
                                    <th style="text-align:left;padding:6px 12px 6px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Arrival</th>
                                    <th style="text-align:left;padding:6px 0 6px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">VIP / Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                    ['Mr. David Lim',       '801 River Suite', '11:00', 'VIP — Keynote speaker (Pharma summit)', true],
                                    ['H.E. Minister Sokha', '702 Exec. Suite', '14:00', 'State protocol required',               true],
                                    ['Ms. Aiko Tanaka',     '512',             '15:30', 'Pharma summit delegate',                false],
                                    ['Mr. & Mrs. Beaumont', '320',             '16:00', 'Honeymoon amenity requested',           false],
                                    ['Dr. Rajan Patel',     '415',             '13:00', 'Summit presenter',                      false],
                                ] as [$name, $room, $time, $note, $vip])
                                <tr style="border-bottom:1px solid #f9f9f8;">
                                    <td style="padding:9px 12px 9px 0;font-weight:600;color:#0a0a0a;">
                                        @if($vip)<span style="font-size:8px;font-weight:700;padding:2px 5px;border-radius:4px;background:#fef3c7;color:#d97706;margin-right:5px;">VIP</span>@endif
                                        {{ $name }}
                                    </td>
                                    <td style="padding:9px 12px 9px 0;color:#737373;">{{ $room }}</td>
                                    <td style="padding:9px 12px 9px 0;color:#737373;white-space:nowrap;">{{ $time }}</td>
                                    <td style="padding:9px 0;color:#737373;">{{ $note }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
                                   color:#dc2626;margin:0 0 10px;display:flex;align-items:center;gap:6px;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#dc2626;display:inline-block;"></span>
                            Departures (9)
                        </p>
                        <table style="width:100%;border-collapse:collapse;font-size:11px;">
                            <thead>
                                <tr style="color:#a3a3a3;font-size:10px;text-transform:uppercase;letter-spacing:.06em;">
                                    <th style="text-align:left;padding:6px 12px 6px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Guest Name</th>
                                    <th style="text-align:left;padding:6px 12px 6px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Room</th>
                                    <th style="text-align:left;padding:6px 12px 6px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Checkout by</th>
                                    <th style="text-align:left;padding:6px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                    ['Ms. Rachel Green',     '610',       '12:00', 'Late checkout approved — 14:00'],
                                    ['Mr. Kim Sung-jin',     '405',       '11:00', 'Bill settled'],
                                    ['Delegation — 3 pax',   '701 & 703', '10:00', 'Gov. affairs — express checkout'],
                                ] as [$name, $room, $time, $note])
                                <tr style="border-bottom:1px solid #f9f9f8;">
                                    <td style="padding:9px 12px 9px 0;font-weight:600;color:#0a0a0a;">{{ $name }}</td>
                                    <td style="padding:9px 12px 9px 0;color:#737373;">{{ $room }}</td>
                                    <td style="padding:9px 12px 9px 0;color:#737373;">{{ $time }}</td>
                                    <td style="padding:9px 0;color:#737373;">{{ $note }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 3. Banquet & MICE Snapshot ──────────────────────────── --}}
        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
            <button class="ops-toggle" data-target="ops-banquet"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;
                           background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                    onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
                <div class="ops-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;
                                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg class="ops-arrow" style="width:9px;height:9px;transition:transform .2s;"
                         fill="currentColor" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Banquet &amp; MICE Snapshot</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Events, venues &amp; pipeline</p>
                </div>
                <span style="font-size:11px;font-weight:600;color:#a3a3a3;flex-shrink:0;">3 events · $48.7K</span>
            </button>
            <div id="ops-banquet" class="ops-body" style="display:none;border-top:1px solid #f0f0ef;">
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">

                    {{-- 3 event cards --}}
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                        @foreach([
                            ['International Pharma Leadership Summit','Novara Group Asia','Grand Ballroom A+B','08:00–18:30','320','$28,400','Ready','#16a34a','#dcfce7'],
                            ['Khmer Heritage Wedding (Chan & Lyna)','Direct client','River Terrace + Crystal Hall','17:00–23:00','250','$16,200','Setup','#d97706','#fef3c7'],
                            ['Ministry of Commerce Board Retreat','Gov. Affairs Office','Executive Boardroom 1','09:00–17:00','22','$4,100','Ready','#16a34a','#dcfce7'],
                        ] as [$title,$org,$venue,$time,$pax,$rev,$status,$sc,$sb])
                        <div style="background:#f9f9f8;border-radius:12px;padding:14px;">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:10px;">
                                <p style="font-size:11px;font-weight:700;color:#0a0a0a;margin:0;line-height:1.4;">{{ $title }}</p>
                                <span style="font-size:8px;font-weight:700;padding:3px 7px;border-radius:100px;white-space:nowrap;flex-shrink:0;
                                             color:{{ $sc }};background:{{ $sb }};">{{ $status }}</span>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:3px;font-size:10px;color:#737373;">
                                <p style="margin:0;">📍 {{ $venue }}</p>
                                <p style="margin:0;">🕐 {{ $time }} · {{ $pax }} pax</p>
                                <p style="margin:0;font-weight:700;color:#0a0a0a;">{{ $rev }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Sub: MICE Upcoming --}}
                    <div style="border:1px solid #e5e5e3;border-radius:12px;overflow:hidden;">
                        <button class="ops-toggle" data-target="ops-mice"
                                style="width:100%;display:flex;align-items:center;gap:10px;padding:12px 16px;
                                       background:#f9f9f8;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                                onmouseover="this.style.background='#f3f3f2'" onmouseout="this.style.background='#f9f9f8'">
                            <div class="ops-arrow-wrap" style="width:20px;height:20px;border-radius:6px;background:#ededec;
                                                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg class="ops-arrow" style="width:8px;height:8px;transition:transform .2s;"
                                     fill="currentColor" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                            </div>
                            <p style="font-size:12px;font-weight:600;color:#0a0a0a;margin:0;flex:1;">MICE – Upcoming Events</p>
                            <span style="font-size:10px;color:#a3a3a3;">9 events · $186K pipeline</span>
                        </button>
                        <div id="ops-mice" class="ops-body" style="display:none;border-top:1px solid #e5e5e3;">
                            <div style="padding:14px 16px;overflow-x:auto;">
                                <table style="width:100%;border-collapse:collapse;font-size:11px;min-width:480px;">
                                    <thead>
                                        <tr style="color:#a3a3a3;font-size:10px;text-transform:uppercase;letter-spacing:.06em;">
                                            <th style="text-align:left;padding:5px 12px 5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Event</th>
                                            <th style="text-align:left;padding:5px 12px 5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Date</th>
                                            <th style="text-align:left;padding:5px 12px 5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Venue</th>
                                            <th style="text-align:left;padding:5px 12px 5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Pax</th>
                                            <th style="text-align:left;padding:5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach([
                                            ['Tech Startup Demo Day','03 May','Innovation Hub','180','$12,400'],
                                            ['ASEAN Tourism Forum','05 May','Grand Ballroom A','400','$38,000'],
                                            ['Pharma Regional Sales Conf.','07 May','Ballroom B + Breakouts','150','$21,600'],
                                            ['Law Firm Annual Partners Meet','09 May','Executive Boardroom','35','$5,800'],
                                            ['NGO Capacity Building Workshop','12 May','Crystal Hall','80','$9,200'],
                                        ] as [$name,$date,$venue,$pax,$rev])
                                        <tr style="border-bottom:1px solid #f9f9f8;">
                                            <td style="padding:8px 12px 8px 0;font-weight:600;color:#0a0a0a;">{{ $name }}</td>
                                            <td style="padding:8px 12px 8px 0;color:#737373;">{{ $date }}</td>
                                            <td style="padding:8px 12px 8px 0;color:#737373;">{{ $venue }}</td>
                                            <td style="padding:8px 12px 8px 0;color:#737373;">{{ $pax }}</td>
                                            <td style="padding:8px 0;font-weight:700;color:#0a0a0a;">{{ $rev }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Sub: Venue Status --}}
                    <div style="border:1px solid #e5e5e3;border-radius:12px;overflow:hidden;">
                        <button class="ops-toggle" data-target="ops-venue"
                                style="width:100%;display:flex;align-items:center;gap:10px;padding:12px 16px;
                                       background:#f9f9f8;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                                onmouseover="this.style.background='#f3f3f2'" onmouseout="this.style.background='#f9f9f8'">
                            <div class="ops-arrow-wrap" style="width:20px;height:20px;border-radius:6px;background:#ededec;
                                                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg class="ops-arrow" style="width:8px;height:8px;transition:transform .2s;"
                                     fill="currentColor" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                            </div>
                            <p style="font-size:12px;font-weight:600;color:#0a0a0a;margin:0;flex:1;">Venue &amp; Function Space Status</p>
                        </button>
                        <div id="ops-venue" class="ops-body" style="display:none;border-top:1px solid #e5e5e3;">
                            <div style="padding:14px 16px;display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                                @foreach([
                                    ['Grand Ballroom A+B',    'In Use',    '#dc2626','#fee2e2'],
                                    ['Crystal Hall',          'In Use',    '#dc2626','#fee2e2'],
                                    ['River Terrace',         'Setup',     '#d97706','#fef3c7'],
                                    ['Executive Boardroom 1', 'In Use',    '#dc2626','#fee2e2'],
                                    ['Executive Boardroom 2', 'Available', '#16a34a','#dcfce7'],
                                    ['Innovation Hub',        'Available', '#16a34a','#dcfce7'],
                                    ['Rooftop Lounge',        'Available', '#16a34a','#dcfce7'],
                                    ['Breakout Room A',       'In Use',    '#dc2626','#fee2e2'],
                                    ['Breakout Room B',       'Setup',     '#d97706','#fef3c7'],
                                ] as [$v,$s,$c,$b])
                                <div style="display:flex;align-items:center;justify-content:space-between;
                                             background:#f9f9f8;border-radius:8px;padding:8px 10px;">
                                    <span style="font-size:10px;font-weight:600;color:#0a0a0a;">{{ $v }}</span>
                                    <span style="font-size:8px;font-weight:700;padding:2px 7px;border-radius:100px;
                                                 color:{{ $c }};background:{{ $b }};">{{ $s }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── 4. Food & Beverage ───────────────────────────────────── --}}
        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
            <button class="ops-toggle" data-target="ops-fnb"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;
                           background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                    onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
                <div class="ops-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;
                                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg class="ops-arrow" style="width:9px;height:9px;transition:transform .2s;"
                         fill="currentColor" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Food &amp; Beverage Outlet Snapshot</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Covers, food cost &amp; outlet status</p>
                </div>
                <span style="font-size:11px;font-weight:600;color:#a3a3a3;flex-shrink:0;">772 covers today</span>
            </button>
            <div id="ops-fnb" class="ops-body" style="display:none;border-top:1px solid #f0f0ef;">
                <div style="padding:16px 20px;">
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px;">
                        @foreach([
                            ['Total Covers','772','Summit + Wedding + Restaurant'],
                            ['Food Cost','31%','Previous day actuals'],
                            ['Special Dietary','8','Requests confirmed'],
                            ['Executive Chef','On duty','Fully staffed'],
                        ] as [$l,$v,$s])
                        <div style="background:#f9f9f8;border-radius:12px;padding:12px;">
                            <p style="font-size:9px;text-transform:uppercase;letter-spacing:.07em;color:#a3a3a3;font-weight:600;margin:0 0 5px;">{{ $l }}</p>
                            <p style="font-size:18px;font-weight:700;color:#0a0a0a;margin:0;line-height:1;">{{ $v }}</p>
                            <p style="font-size:10px;color:#a3a3a3;margin:4px 0 0;">{{ $s }}</p>
                        </div>
                        @endforeach
                    </div>
                    <table style="width:100%;border-collapse:collapse;font-size:11px;">
                        <thead>
                            <tr style="color:#a3a3a3;font-size:10px;text-transform:uppercase;letter-spacing:.06em;">
                                <th style="text-align:left;padding:5px 12px 5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Outlet</th>
                                <th style="text-align:left;padding:5px 12px 5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Status</th>
                                <th style="text-align:left;padding:5px 12px 5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Covers</th>
                                <th style="text-align:left;padding:5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([
                                ['Riverside Restaurant','Open','~180','Full house for breakfast'],
                                ['Grand Ballroom Kitchen','Active','320','Pharma summit banquet service'],
                                ['River Terrace Kitchen','Prep','250','Wedding service from 17:00'],
                                ['Executive Lounge','Open','22','Ministry delegation service'],
                                ['Pool Bar','Open','—','Normal operations'],
                                ['In-Room Dining','Open','—','8 special dietary orders queued'],
                            ] as [$outlet,$status,$covers,$note])
                            @php $isOpen = in_array($status, ['Open','Active']); @endphp
                            <tr style="border-bottom:1px solid #f9f9f8;">
                                <td style="padding:8px 12px 8px 0;font-weight:600;color:#0a0a0a;">{{ $outlet }}</td>
                                <td style="padding:8px 12px 8px 0;">
                                    <span style="font-size:8px;font-weight:700;padding:2px 7px;border-radius:100px;
                                                 color:{{ $isOpen ? '#16a34a' : '#d97706' }};
                                                 background:{{ $isOpen ? '#dcfce7' : '#fef3c7' }};">{{ $status }}</span>
                                </td>
                                <td style="padding:8px 12px 8px 0;color:#737373;">{{ $covers }}</td>
                                <td style="padding:8px 0;color:#737373;">{{ $note }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── 5. Wellness Snapshot ─────────────────────────────────── --}}
        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:14px;overflow:hidden;">
            <button class="ops-toggle" data-target="ops-wellness"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;
                           background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                    onmouseover="this.style.background='#f9f9f8'" onmouseout="this.style.background='transparent'">
                <div class="ops-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#f3f3f2;
                                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg class="ops-arrow" style="width:9px;height:9px;transition:transform .2s;"
                         fill="currentColor" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Wellness Snapshot</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Spa, pool &amp; fitness facilities</p>
                </div>
                <span style="font-size:11px;font-weight:600;color:#a3a3a3;flex-shrink:0;">78% spa occ · 34 treatments</span>
            </button>
            <div id="ops-wellness" class="ops-body" style="display:none;border-top:1px solid #f0f0ef;">
                <div style="padding:16px 20px;">
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px;">
                        @foreach([
                            ['Spa Occupancy','78%','7 of 9 rooms booked'],
                            ['Pool Status','Open','Cleaned · 28°C'],
                            ['Treatments Today','34','Bookings confirmed'],
                            ['Revenue Today','$3.2K','On par with target'],
                        ] as [$l,$v,$s])
                        <div style="background:#f9f9f8;border-radius:12px;padding:12px;">
                            <p style="font-size:9px;text-transform:uppercase;letter-spacing:.07em;color:#a3a3a3;font-weight:600;margin:0 0 5px;">{{ $l }}</p>
                            <p style="font-size:18px;font-weight:700;color:#0a0a0a;margin:0;line-height:1;">{{ $v }}</p>
                            <p style="font-size:10px;color:#a3a3a3;margin:4px 0 0;">{{ $s }}</p>
                        </div>
                        @endforeach
                    </div>
                    <table style="width:100%;border-collapse:collapse;font-size:11px;">
                        <thead>
                            <tr style="color:#a3a3a3;font-size:10px;text-transform:uppercase;letter-spacing:.06em;">
                                <th style="text-align:left;padding:5px 12px 5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Facility</th>
                                <th style="text-align:left;padding:5px 12px 5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Status</th>
                                <th style="text-align:left;padding:5px 0;font-weight:600;border-bottom:1px solid #f0f0ef;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([
                                ['Spa & Treatment Rooms','Open','7 therapists on duty, 1 on break'],
                                ['Swimming Pool','Open','Cleaned, temp 28°C, lifeguard on duty'],
                                ['Fitness Centre','Open','Normal hours 06:00–22:00'],
                                ['Sauna & Steam Room','Open','Maintenance check completed'],
                                ['Yoga Pavilion','Booked','Corporate wellness session 09:00–10:30'],
                            ] as [$fac,$status,$note])
                            <tr style="border-bottom:1px solid #f9f9f8;">
                                <td style="padding:8px 12px 8px 0;font-weight:600;color:#0a0a0a;">{{ $fac }}</td>
                                <td style="padding:8px 12px 8px 0;">
                                    <span style="font-size:8px;font-weight:700;padding:2px 7px;border-radius:100px;
                                                 color:{{ $status === 'Open' ? '#16a34a' : '#d97706' }};
                                                 background:{{ $status === 'Open' ? '#dcfce7' : '#fef3c7' }};">{{ $status }}</span>
                                </td>
                                <td style="padding:8px 0;color:#737373;">{{ $note }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── 6. Priority Alerts ───────────────────────────────────── --}}
        <div style="background:#fff;border:1px solid #fecaca;border-radius:14px;overflow:hidden;">
            <button class="ops-toggle" data-target="ops-alerts"
                    style="width:100%;display:flex;align-items:center;gap:12px;padding:16px 20px;
                           background:transparent;border:none;cursor:pointer;text-align:left;transition:background .15s;"
                    onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='transparent'">
                <div class="ops-arrow-wrap" style="width:24px;height:24px;border-radius:8px;background:#fee2e2;
                                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg class="ops-arrow" style="width:9px;height:9px;transition:transform .2s;color:#dc2626;"
                         fill="currentColor" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0a0a0a;margin:0;">Priority Alerts Requiring Chairman's Awareness</p>
                    <p style="font-size:10px;color:#a3a3a3;margin:2px 0 0;">Immediate action or awareness required</p>
                </div>
                <span style="font-size:9px;font-weight:700;padding:4px 10px;border-radius:100px;
                             background:#dc2626;color:#fff;flex-shrink:0;">5 alerts</span>
            </button>
            <div id="ops-alerts" class="ops-body" style="display:none;border-top:1px solid #fecaca;">
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:8px;">
                    @foreach([
                        ['Protocol security sweep for Ministry delegation requires GM approval','Before 08:30 — awaiting sign-off','critical','#dc2626','#fee2e2','#fecaca'],
                        ['Tech Startup Demo Day deposit ($3,200) overdue — client unresponsive','Escalate to Sales Manager immediately','high','#d97706','#fef3c7','#fde68a'],
                        ['Wedding florist delivery expected 15:00 — tight window before 17:00 setup','Assign F&B coordinator to monitor','medium','#2563eb','#dbeafe','#bfdbfe'],
                        ['Additional LED screens confirmed by 09:00 for summit AV setup','Vendor on-site, supervise installation','medium','#2563eb','#dbeafe','#bfdbfe'],
                        ['VIP keynote speaker Mr. David Lim arriving 11:00 — River Suite 801','Protocol prepared, GM notified','info','#16a34a','#dcfce7','#bbf7d0'],
                    ] as [$msg,$action,$level,$tc,$bg,$bd])
                    <div style="display:flex;gap:12px;padding:12px 14px;border-radius:12px;
                                border:1px solid {{ $bd }};background:{{ $bg }};">
                        <div style="width:6px;border-radius:3px;flex-shrink:0;background:{{ $tc }};"></div>
                        <div style="flex:1;">
                            <p style="font-size:12px;font-weight:600;color:#0a0a0a;margin:0 0 3px;line-height:1.5;">{{ $msg }}</p>
                            <p style="font-size:10px;color:#737373;margin:0;">{{ $action }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>{{-- end sections --}}

    {{-- ── Footer summary ───────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:16px;">
        @foreach([
            ['Staff On Duty','88','3 called out · 1 open position'],
            ['External Contractors','18','AV & décor teams on-site'],
            ['Cash Position','$214K','As of this morning'],
            ['Payables This Week','$18.4K','Due within 7 days'],
        ] as [$l,$v,$s])
        <div style="background:#fff;border:1px solid #e5e5e3;border-radius:12px;padding:14px 16px;">
            <p style="font-size:9px;text-transform:uppercase;letter-spacing:.07em;color:#a3a3a3;font-weight:600;margin:0 0 5px;">{{ $l }}</p>
            <p style="font-size:20px;font-weight:700;color:#0a0a0a;margin:0;line-height:1;">{{ $v }}</p>
            <p style="font-size:10px;color:#a3a3a3;margin:5px 0 0;">{{ $s }}</p>
        </div>
        @endforeach
    </div>

</div>

@push('scripts')
<script>
// ── Custom Date Range Picker ──────────────────────────────────────────────────
;(function () {
    const MONTHS = ['January','February','March','April','May','June',
                    'July','August','September','October','November','December']
    const DAYS   = ['Su','Mo','Tu','We','Th','Fr','Sa']

    let startDate  = new Date('{{ $from->format('Y-m-d') }}')
    let endDate    = new Date('{{ $to->format('Y-m-d') }}')
    let hoverDate  = null
    let selecting  = false  // true = waiting for end date
    let leftYear   = startDate.getFullYear()
    let leftMonth  = startDate.getMonth()

    const trigger   = document.getElementById('drp-trigger')
    const dropdown  = document.getElementById('drp-dropdown')
    const fromVal   = document.getElementById('drp-from-val')
    const toVal     = document.getElementById('drp-to-val')
    const fromLabel = document.getElementById('drp-from-label')
    const toLabel   = document.getElementById('drp-to-label')
    const rangeLabel= document.getElementById('drp-range-label')

    function fmt(d) {
        return d ? d.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : ''
    }
    function ymd(d) {
        return d ? d.toISOString().slice(0,10) : ''
    }
    function sameDay(a,b) {
        return a && b && a.toDateString() === b.toDateString()
    }
    function between(d, a, b) {
        if (!a || !b) return false
        const lo = a < b ? a : b, hi = a < b ? b : a
        return d > lo && d < hi
    }

    function updateLabels() {
        fromLabel.textContent = fmt(startDate)
        toLabel.textContent   = fmt(endDate)
        const days = endDate && startDate
            ? Math.round(Math.abs(endDate - startDate) / 86400000)
            : 0
        rangeLabel.textContent = days > 0 ? days + ' day' + (days !== 1 ? 's' : '') : ''
    }

    function renderMonth(containerId, year, month, isRight) {
        const container = document.getElementById(containerId)
        const today = new Date(); today.setHours(0,0,0,0)
        const firstDay = new Date(year, month, 1).getDay()
        const daysInMonth = new Date(year, month+1, 0).getDate()

        let html = `
        <div style="margin-bottom:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                ${isRight ? '<div style="width:28px;"></div>' :
                    `<button type="button" class="drp-nav" data-dir="-1"
                             style="width:28px;height:28px;border-radius:8px;border:1px solid #e5e5e3;
                                    background:#fff;cursor:pointer;display:flex;align-items:center;
                                    justify-content:center;color:#737373;transition:all .15s;"
                             onmouseover="this.style.background='#f3f3f2'" onmouseout="this.style.background='#fff'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>`}
                <span style="font-size:14px;font-weight:700;color:#0a0a0a;">
                    ${MONTHS[month]} ${year}
                </span>
                ${!isRight ? '<div style="width:28px;"></div>' :
                    `<button type="button" class="drp-nav" data-dir="1"
                             style="width:28px;height:28px;border-radius:8px;border:1px solid #e5e5e3;
                                    background:#fff;cursor:pointer;display:flex;align-items:center;
                                    justify-content:center;color:#737373;transition:all .15s;"
                             onmouseover="this.style.background='#f3f3f2'" onmouseout="this.style.background='#fff'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>`}
            </div>
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:0;margin-bottom:6px;">
                ${DAYS.map(d => `<div style="text-align:center;font-size:11px;font-weight:600;
                    color:#a3a3a3;padding:4px 0;">${d}</div>`).join('')}
            </div>
            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;">`

        for (let i = 0; i < firstDay; i++) {
            html += `<div style="height:36px;"></div>`
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const d = new Date(year, month, day)
            d.setHours(0,0,0,0)
            const isToday   = sameDay(d, today)
            const isStart   = sameDay(d, startDate)
            const isEnd     = sameDay(d, endDate)
            const isSelected= isStart || isEnd
            const effectiveEnd = selecting && hoverDate ? hoverDate : endDate
            const inRange   = between(d, startDate, effectiveEnd)
            const isFuture  = d > today

            let bg = 'transparent', color = '#0a0a0a', border = 'none', radius = '8px'
            let fontWeight = '400'

            if (isSelected) {
                bg = '#0a0a0a'; color = '#fff'; fontWeight = '700'
            } else if (inRange) {
                bg = '#f3f3f2'; color = '#0a0a0a'
            } else if (isToday) {
                border = '1.5px solid #d1d5db'
            }

            if (isFuture) { color = '#d1d5db'; bg = 'transparent' }

            html += `<div class="drp-day" data-date="${ymd(d)}"
                style="height:36px;display:flex;align-items:center;justify-content:center;
                       font-size:13px;font-weight:${fontWeight};color:${color};
                       background:${bg};border:${border};border-radius:${radius};
                       cursor:${isFuture ? 'default' : 'pointer'};
                       transition:background .1s,color .1s;user-select:none;">
                ${day}
            </div>`
        }

        html += `</div></div>`
        container.innerHTML = html

        // Nav buttons
        container.querySelectorAll('.drp-nav').forEach(btn => {
            btn.addEventListener('click', () => {
                leftMonth += parseInt(btn.dataset.dir)
                if (leftMonth > 11) { leftMonth = 0; leftYear++ }
                if (leftMonth < 0)  { leftMonth = 11; leftYear-- }
                renderBoth()
            })
        })

        // Day clicks
        container.querySelectorAll('.drp-day').forEach(cell => {
            const d = new Date(cell.dataset.date)
            if (d > today) return

            cell.addEventListener('mouseenter', () => {
                if (selecting) { hoverDate = d; renderBoth() }
            })
            cell.addEventListener('click', () => {
                if (!selecting) {
                    startDate = d; endDate = null; selecting = true
                } else {
                    if (d < startDate) { endDate = startDate; startDate = d }
                    else               { endDate = d }
                    selecting = false; hoverDate = null
                    updateLabels()
                }
                renderBoth()
            })
        })
    }

    function renderBoth() {
        const rightYear  = leftMonth === 11 ? leftYear + 1 : leftYear
        const rightMonth = leftMonth === 11 ? 0 : leftMonth + 1
        renderMonth('drp-cal-left',  leftYear,  leftMonth,  false)
        renderMonth('drp-cal-right', rightYear, rightMonth, true)
    }

    // Prevent any click inside the dropdown from closing it
    dropdown.addEventListener('click', e => e.stopPropagation())

    // Open / close
    trigger.addEventListener('click', e => {
        e.stopPropagation()
        const open = dropdown.style.display !== 'none'
        dropdown.style.display = open ? 'none' : 'block'
        if (!open) renderBoth()
    })
    document.addEventListener('click', () => {
        dropdown.style.display = 'none'
        selecting = false; hoverDate = null
    })
    document.getElementById('drp-cancel').addEventListener('click', () => {
        dropdown.style.display = 'none'
        selecting = false; hoverDate = null
    })

    // Apply
    document.getElementById('drp-apply').addEventListener('click', () => {
        const s = startDate
        const e = endDate || startDate   // single-day if no end yet
        if (!s) return
        const lo = s <= e ? s : e
        const hi = s <= e ? e : s
        fromVal.value = ymd(lo)
        toVal.value   = ymd(hi)
        selecting = false
        hoverDate = null
        dropdown.style.display = 'none'
        document.getElementById('range-form').submit()
    })

    // Presets
    document.querySelectorAll('.drp-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            const today = new Date(); today.setHours(0,0,0,0)
            const preset = btn.dataset.preset
            if (preset === 'today') {
                startDate = new Date(today); endDate = new Date(today)
            } else if (preset === 'week') {
                startDate = new Date(today); startDate.setDate(today.getDate()-6)
                endDate   = new Date(today)
            } else if (preset === 'month') {
                startDate = new Date(today.getFullYear(), today.getMonth(), 1)
                endDate   = new Date(today)
            } else if (preset === '30days') {
                startDate = new Date(today); startDate.setDate(today.getDate()-29)
                endDate   = new Date(today)
            }
            leftYear  = startDate.getFullYear()
            leftMonth = startDate.getMonth()
            selecting = false; hoverDate = null
            updateLabels(); renderBoth()
        })
    })

    updateLabels()
})()

// ── Section accordion ─────────────────────────────────────────────────────────
document.querySelectorAll('.ops-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const id    = btn.dataset.target
        const body  = document.getElementById(id)
        const arrow = btn.querySelector('.ops-arrow')
        const wrap  = btn.querySelector('.ops-arrow-wrap')
        if (!body) return
        const open = body.style.display !== 'none'
        body.style.display    = open ? 'none' : 'block'
        arrow.style.transform = open ? '' : 'rotate(90deg)'
        arrow.style.filter    = open ? '' : 'invert(1)'
        if (wrap) {
            if (!wrap.dataset.origBg) wrap.dataset.origBg = wrap.style.background
            wrap.style.background = open ? wrap.dataset.origBg : '#0a0a0a'
        }
    })
})

// ── Metric card toggles ───────────────────────────────────────────────────────
const METRIC_KEY = 'ops_metrics_hidden_v1'

function getHidden() {
    try { return JSON.parse(localStorage.getItem(METRIC_KEY)) || [] } catch { return [] }
}
function saveHidden(arr) {
    localStorage.setItem(METRIC_KEY, JSON.stringify(arr))
}

function applyMetricState() {
    const hidden = getHidden()
    document.querySelectorAll('.ops-metric-card').forEach(card => {
        const id  = card.dataset.metricId
        const btn = document.querySelector(`.ops-metric-toggle[data-metric-id="${id}"]`)
        const off = hidden.includes(id)

        card.style.opacity = off ? '0.35' : '1'
        card.style.filter  = off ? 'grayscale(1)' : ''

        if (btn) {
            const thumb = btn.querySelector('span')
            if (off) {
                btn.style.background   = '#d1d5db'
                if (thumb) thumb.style.left = '2px'
            } else {
                btn.style.background   = '#10b981'
                if (thumb) thumb.style.left = '17px'
            }
        }
    })
}

document.querySelectorAll('.ops-metric-toggle').forEach(btn => {
    btn.addEventListener('click', e => {
        e.stopPropagation()
        const id     = btn.dataset.metricId
        const hidden = getHidden()
        const idx    = hidden.indexOf(id)
        if (idx === -1) hidden.push(id)
        else            hidden.splice(idx, 1)
        saveHidden(hidden)
        applyMetricState()
    })
})

applyMetricState()
</script>
@endpush
@endsection
