@extends('layouts.app')
@section('title', $report->period_label)
@section('page-title', 'Monthly Report')
@section('page-sub', $report->period_label . ' — ' . ($report->department?->label ?? 'No Department'))

@section('content')
<div class="space-y-4">

    {{-- Header card --}}
    <div class="card">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-1">
                <p class="text-[15px] font-bold text-brand-black">{{ $report->period_label }}</p>
                <p class="text-[11px] text-brand-muted">
                    {{ $report->department?->label ?? '—' }}
                    @if($report->report_type)
                        · <span class="uppercase">{{ $report->report_type }}</span>
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2">
                @php
                    $statusColors = [
                        'draft'        => 'text-status-orange bg-status-orange-bg border-status-orange/20',
                        'finalized'    => 'text-status-blue bg-status-blue-bg border-status-blue/20',
                        'acknowledged' => 'text-status-green bg-status-green-bg border-status-green/20',
                    ];
                    $sc = $statusColors[$report->status] ?? 'text-brand-muted bg-brand-bg border-brand-border';
                @endphp
                <span class="text-[10px] font-semibold px-2.5 py-1 rounded border uppercase {{ $sc }}">
                    {{ $report->status }}
                </span>
                @if($report->pdf_path)
                    <a href="{{ route('reports.download', $report) }}" class="btn-secondary h-8 text-[11px]">
                        Download PDF
                    </a>
                @endif
                <a href="{{ route('reports.index') }}" class="btn-secondary h-8 text-[11px]">← Back</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Commentary --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="card">
                <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide mb-2">Commentary</p>
                @if($report->commentary)
                    <p class="text-[12px] text-brand-black leading-relaxed whitespace-pre-line">{{ $report->commentary }}</p>
                @else
                    <p class="text-[11px] text-brand-subtle italic">No commentary added yet.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-3">

            {{-- Details --}}
            <div class="card">
                <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide mb-3">Details</p>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-[11px] text-brand-muted">Period</dt>
                        <dd class="text-[11px] font-medium text-brand-black">{{ $report->period_label }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-[11px] text-brand-muted">Department</dt>
                        <dd class="text-[11px] font-medium text-brand-black">{{ $report->department?->label ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-[11px] text-brand-muted">Type</dt>
                        <dd class="text-[11px] font-medium text-brand-black uppercase">{{ $report->report_type ?? 'monthly' }}</dd>
                    </div>
                    @if($report->generated_at)
                        <div class="flex justify-between">
                            <dt class="text-[11px] text-brand-muted">Generated</dt>
                            <dd class="text-[11px] text-brand-black">{{ $report->generated_at->format('d M Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Finalization --}}
            @if($report->finalized_at)
                <div class="card">
                    <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide mb-3">Finalized</p>
                    <p class="text-[11px] text-brand-black">{{ $report->finalizedBy?->full_name ?? '—' }}</p>
                    <p class="text-[10px] text-brand-subtle mt-0.5">{{ $report->finalized_at->format('d M Y H:i') }}</p>
                </div>
            @endif

            {{-- Acknowledgement --}}
            @if($report->acknowledged_at)
                <div class="card">
                    <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide mb-3">Acknowledged</p>
                    <p class="text-[11px] text-brand-black">{{ $report->acknowledgedBy?->full_name ?? '—' }}</p>
                    <p class="text-[10px] text-brand-subtle mt-0.5">{{ $report->acknowledged_at->format('d M Y H:i') }}</p>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
