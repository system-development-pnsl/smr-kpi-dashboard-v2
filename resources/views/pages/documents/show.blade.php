@extends('layouts.app')
@section('title', $document->original_name)
@section('page-title', 'AI Extraction Review')
@section('page-sub', $document->original_name)

@section('content')
<div class="space-y-4">

    {{-- Meta card --}}
    <div class="card">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded bg-brand-bg border border-brand-border flex items-center justify-center flex-shrink-0">
                        <span class="text-[9px] font-bold text-brand-muted">{{ $document->file_type }}</span>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-brand-black">{{ $document->original_name }}</p>
                        <p class="text-[11px] text-brand-muted">
                            {{ $document->department?->label }} · {{ $document->size_for_humans }}
                            · Uploaded by {{ $document->uploadedBy?->full_name }} on {{ $document->created_at->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>
                @if($document->description)
                    <p class="text-[12px] text-brand-muted pl-10">{{ $document->description }}</p>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @php
                    $statusColors = [
                        'pending'    => 'text-status-orange bg-status-orange-bg border-status-orange/20',
                        'processing' => 'text-status-blue bg-status-blue-bg border-status-blue/20',
                        'extracted'  => 'text-status-blue bg-status-blue-bg border-status-blue/20',
                        'review'     => 'text-status-amber bg-status-amber-bg border-status-amber/20',
                        'confirmed'  => 'text-status-green bg-status-green-bg border-status-green/20',
                        'failed'     => 'text-status-red bg-status-red-bg border-status-red/20',
                    ];
                    $sc = $statusColors[$document->ai_status] ?? 'text-brand-muted bg-brand-bg border-brand-border';
                @endphp
                <span id="ai-status-badge" class="text-[10px] font-semibold px-2.5 py-1 rounded border uppercase {{ $sc }}">
                    AI: {{ $document->ai_status }}
                </span>
                <a href="{{ route('documents.download', $document) }}" class="btn-secondary h-8 text-[11px]">
                    Download
                </a>
                <a href="{{ route('documents.index') }}" class="btn-secondary h-8 text-[11px]">
                    ← Back
                </a>
            </div>
        </div>
    </div>

    {{-- AI summary --}}
    @if($document->extracted_data)
        @php $data = $document->extracted_data; @endphp

        @if(!empty($data['document_summary']))
            <div class="card bg-brand-bg border border-brand-border/60">
                <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide mb-1">AI Summary</p>
                <p class="text-[12px] text-brand-black">{{ $data['document_summary'] }}</p>
            </div>
        @endif

        {{-- Confirm form --}}
        @if(in_array($document->ai_status, ['extracted', 'review']))
            <form method="POST" action="{{ route('documents.confirm', $document) }}" id="confirm-form">
                @csrf

                <div class="card overflow-hidden p-0">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-brand-border">
                        <div>
                            <p class="text-[12px] font-semibold text-brand-black">Extracted Fields</p>
                            <p class="text-[11px] text-brand-muted">Check fields to push to the dashboard</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-1.5 text-[11px] text-brand-muted cursor-pointer select-none">
                                <input type="checkbox"
                                       id="select-all-checkbox"
                                       class="rounded border-brand-border">
                                Select all
                            </label>
                            <button type="submit" class="btn-primary h-8 text-[11px]">
                                Confirm & Push to Dashboard
                            </button>
                        </div>
                    </div>

                    {{-- Header row --}}
                    <div class="grid grid-cols-12 gap-3 table-header">
                        <div class="col-span-1"></div>
                        <div class="col-span-3">Field</div>
                        <div class="col-span-2">Value</div>
                        <div class="col-span-2 hidden sm:block">Unit</div>
                        <div class="col-span-1 hidden md:block">Confidence</div>
                        <div class="col-span-2 hidden md:block">Target</div>
                        <div class="col-span-1 hidden lg:block">Period</div>
                    </div>

                    @foreach($data['extracted_fields'] ?? [] as $i => $field)
                        @php
                            $conf = (float)($field['confidence'] ?? 0);
                            $confColor = $conf >= 0.9 ? 'text-status-green' : ($conf >= 0.7 ? 'text-status-orange' : 'text-status-red');
                        @endphp
                        <div class="data-row grid grid-cols-12 gap-3 items-center">
                            {{-- Checkbox --}}
                            <div class="col-span-1 flex items-center justify-center">
                                <input type="checkbox"
                                       name="confirmed_fields[]"
                                       value="{{ $i }}"
                                       {{ $conf >= 0.8 ? 'checked' : '' }}
                                       class="rounded border-brand-border">
                                <input type="hidden" name="confirmed_fields_data[{{ $i }}][field_name]"   value="{{ $field['field_name'] ?? '' }}">
                                <input type="hidden" name="confirmed_fields_data[{{ $i }}][field_key]"    value="{{ $field['field_key'] ?? '' }}">
                                <input type="hidden" name="confirmed_fields_data[{{ $i }}][value]"        value="{{ $field['value'] ?? '' }}">
                                <input type="hidden" name="confirmed_fields_data[{{ $i }}][unit]"         value="{{ $field['unit'] ?? '' }}">
                                <input type="hidden" name="confirmed_fields_data[{{ $i }}][period]"       value="{{ $field['period'] ?? '' }}">
                                <input type="hidden" name="confirmed_fields_data[{{ $i }}][target_module]" value="{{ $field['target_module'] ?? '' }}">
                                <input type="hidden" name="confirmed_fields_data[{{ $i }}][confidence]"   value="{{ $conf }}">
                            </div>

                            {{-- Field name --}}
                            <div class="col-span-3 min-w-0">
                                <p class="text-[12px] font-medium text-brand-black truncate">{{ $field['field_name'] ?? '—' }}</p>
                                <p class="text-[10px] text-brand-subtle truncate font-mono">{{ $field['field_key'] ?? '' }}</p>
                            </div>

                            {{-- Value --}}
                            <div class="col-span-2">
                                @if($field['value'] !== null)
                                    <span class="text-[12px] font-semibold text-brand-black">{{ $field['value'] }}</span>
                                @else
                                    <span class="text-[11px] text-brand-subtle italic">null</span>
                                @endif
                            </div>

                            {{-- Unit --}}
                            <div class="col-span-2 hidden sm:block">
                                <span class="text-[11px] text-brand-muted">{{ $field['unit'] ?? '—' }}</span>
                            </div>

                            {{-- Confidence --}}
                            <div class="col-span-1 hidden md:block">
                                <span class="text-[11px] font-semibold {{ $confColor }}">
                                    {{ number_format($conf * 100) }}%
                                </span>
                            </div>

                            {{-- Target module --}}
                            <div class="col-span-2 hidden md:block">
                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-brand-bg text-brand-muted uppercase">
                                    {{ $field['target_module'] ?? 'none' }}
                                </span>
                            </div>

                            {{-- Period --}}
                            <div class="col-span-1 hidden lg:block">
                                <span class="text-[10px] text-brand-subtle">{{ $field['period'] ?? '—' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>

        {{-- Already confirmed --}}
        @elseif($document->ai_status === 'confirmed')
            <div class="card">
                <p class="text-[11px] font-semibold text-status-green mb-1">Confirmed & pushed to dashboard</p>
                <p class="text-[11px] text-brand-muted">
                    Confirmed by {{ $document->confirmedBy?->full_name }} on {{ $document->confirmed_at?->format('d M Y H:i') }}
                </p>
            </div>

        {{-- Pending / processing --}}
        @else
            <div class="card text-center py-8" id="processing-card">
                <div class="w-8 h-8 border-2 border-brand-border border-t-brand-black rounded-full animate-spin mx-auto mb-3"></div>
                <p class="text-[12px] text-brand-muted" id="processing-label">AI is processing this document…</p>
                <p class="text-[11px] text-brand-subtle mt-1">You'll be notified automatically when done.</p>
            </div>
        @endif

        {{-- Unrecognized items --}}
        @if(!empty($data['unrecognized_items']))
            <div class="card">
                <p class="text-[10px] font-semibold text-brand-muted uppercase tracking-wide mb-2">Unrecognized Items</p>
                <ul class="space-y-1">
                    @foreach($data['unrecognized_items'] as $item)
                        <li class="text-[11px] text-brand-muted">· {{ is_array($item) ? json_encode($item) : $item }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    {{-- No extraction yet --}}
    @else
        <div class="card text-center py-10" id="processing-card">
            <div class="w-8 h-8 border-2 border-brand-border border-t-brand-black rounded-full animate-spin mx-auto mb-3"></div>
            <p class="text-[12px] text-brand-muted" id="processing-label">AI extraction has not run yet or is still processing.</p>
            <p class="text-[11px] text-brand-subtle mt-1">You'll be notified automatically when done.</p>
        </div>
    @endif

</div>
@endsection

