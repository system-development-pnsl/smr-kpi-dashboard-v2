@extends('layouts.app')
@section('title', 'Monthly Reports')
@section('page-title', 'Monthly Reports')
@section('page-sub', 'Department performance reports by period')

@section('content')
<div class="space-y-4">

    {{-- Reports table --}}
    <div class="card overflow-hidden p-0">
        <div class="grid grid-cols-12 gap-3 table-header">
            <div class="col-span-3">Period</div>
            <div class="col-span-3">Department</div>
            <div class="col-span-2 hidden sm:block">Type</div>
            <div class="col-span-2 hidden md:block">Status</div>
            <div class="col-span-1 hidden md:block">Finalized</div>
            <div class="col-span-1">Actions</div>
        </div>

        @forelse($reports as $report)
            @php
                $statusColors = [
                    'draft'        => 'text-status-orange bg-status-orange-bg',
                    'finalized'    => 'text-status-blue bg-status-blue-bg',
                    'acknowledged' => 'text-status-green bg-status-green-bg',
                ];
                $sc = $statusColors[$report->status] ?? 'text-brand-muted bg-brand-bg';
            @endphp
            <div class="data-row group grid grid-cols-12 gap-3 items-center">

                {{-- Period --}}
                <div class="col-span-3">
                    <a href="{{ route('reports.show', $report) }}"
                       class="text-[12px] font-semibold text-brand-black hover:underline">
                        {{ $report->period_label }}
                    </a>
                </div>

                {{-- Department --}}
                <div class="col-span-3">
                    <span class="text-[11px] text-brand-muted">{{ $report->department?->label ?? '—' }}</span>
                </div>

                {{-- Type --}}
                <div class="col-span-2 hidden sm:flex items-center">
                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-brand-bg border border-brand-border text-brand-muted uppercase">
                        {{ $report->report_type ?? 'monthly' }}
                    </span>
                </div>

                {{-- Status --}}
                <div class="col-span-2 hidden md:flex items-center">
                    <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded uppercase {{ $sc }}">
                        {{ $report->status }}
                    </span>
                </div>

                {{-- Finalized at --}}
                <div class="col-span-1 hidden md:flex items-center">
                    <span class="text-[10px] text-brand-subtle">
                        {{ $report->finalized_at?->format('d M Y') ?? '—' }}
                    </span>
                </div>

                {{-- Actions --}}
                <div class="col-span-1 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a href="{{ route('reports.show', $report) }}"
                       class="p-1 text-brand-muted hover:text-brand-black transition-colors" title="View">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    @if($report->pdf_path)
                        <a href="{{ route('reports.download', $report) }}"
                           class="p-1 text-brand-muted hover:text-brand-black transition-colors" title="Download PDF">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-[12px] text-brand-muted">
                No reports generated yet.
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $reports->links() }}</div>

</div>
@endsection
