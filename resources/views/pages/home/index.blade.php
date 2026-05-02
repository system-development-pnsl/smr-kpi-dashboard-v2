@extends('layouts.app')
@section('title', 'Home')
@section('page-title', 'Home')
@section('page-sub', now()->format('l, d F Y'))

@section('content')
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;
            min-height:calc(100vh - 56px - 40px);text-align:center;padding:40px 20px;">

    {{-- Sun / moon icon --}}
    <div style="width:72px;height:72px;margin-bottom:24px;opacity:0.12;">
        <img src="https://smr-zone.b-cdn.net/wp-content/uploads/2025/09/sun-and-moon-river-side-logo.png"
             alt="" style="width:100%;height:100%;object-fit:contain;display:block;">
    </div>

    {{-- Greeting --}}
    <h1 style="font-size:32px;font-weight:700;color:#0a0a0a;letter-spacing:-0.5px;margin:0 0 6px;">
        {{ $greeting }}, {{ auth()->user()->full_name }}!
    </h1>
    <p style="font-size:13px;color:#737373;margin:0 0 40px;">
        {{ now()->format('l, d F Y') }} &mdash; Sun &amp; Moon Riverside Hotel
    </p>

    {{-- AI search bar --}}
    <form action="{{ route('search') }}" method="GET"
          style="width:100%;max-width:640px;">
        <div style="position:relative;">
            <input type="text" name="q" placeholder="Tell us what you'd like to know today…"
                   autofocus
                   style="width:100%;box-sizing:border-box;
                          padding:18px 64px 18px 28px;
                          font-size:15px;color:#0a0a0a;
                          background:#f5b942;border:none;
                          border-radius:100px;outline:none;
                          box-shadow:0 4px 24px rgba(245,185,66,.35);
                          placeholder-color:#7a5a10;">
            <button type="submit"
                    style="position:absolute;right:8px;top:50%;transform:translateY(-50%);
                           width:44px;height:44px;border-radius:50%;border:none;
                           background:#0a0a0a;cursor:pointer;
                           display:flex;align-items:center;justify-content:center;">
                <svg style="width:16px;height:16px;" fill="none" stroke="#fff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
            </button>
        </div>
        <p style="font-size:11px;color:#a3a3a3;margin-top:12px;">
            Search across tasks, KPIs, reports, and documents
        </p>
    </form>

    {{-- Quick links --}}
    <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-top:48px;">
        @foreach([
            ['label' => 'Dashboard',          'route' => 'dashboard',      'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['label' => 'Operations',         'route' => 'reports.index',  'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'KPI Overview',       'route' => 'kpi.index',      'icon' => 'M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label' => 'Task Management',    'route' => 'tasks.index',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Financial',          'route' => 'financial.index','icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Documents & AI',     'route' => 'documents.index','icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ] as $q)
            <a href="{{ route($q['route']) }}"
               style="display:inline-flex;align-items:center;gap:7px;
                      padding:9px 16px;border-radius:100px;
                      border:1px solid #e5e5e3;background:#fff;
                      font-size:12px;font-weight:500;color:#404040;
                      text-decoration:none;transition:all .15s;"
               onmouseover="this.style.borderColor='#0a0a0a';this.style.color='#0a0a0a';"
               onmouseout="this.style.borderColor='#e5e5e3';this.style.color='#404040';">
                <svg style="width:13px;height:13px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $q['icon'] }}"/>
                </svg>
                {{ $q['label'] }}
            </a>
        @endforeach
    </div>

</div>
@endsection
