@extends('layouts.app')
@section('title', 'Documents & AI')
@section('page-title', 'Documents & AI Extraction')
@section('page-sub', 'Upload reports — AI extracts KPI data automatically')

@section('content')
    <div class="space-y-4">
        <div class="card">
            <p class="text-[11px] font-semibold text-brand-black mb-3">Upload Document</p>
            <form method="POST" action="{{ route('documents.upload') }}" enctype="multipart/form-data"
                class="flex flex-wrap items-end gap-3"
                data-loading-message="AI is analysing your document — this may take up to 30 seconds…">
                @csrf
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-brand-muted font-medium uppercase tracking-wide">Department</label>
                    <x-select-search name="department_id" :options="$departments->map(fn($d) => ['value' => $d->id, 'label' => $d->label])->all()" placeholder="Select department…"
                        :required="true" class="w-52" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-brand-muted font-medium uppercase tracking-wide">File</label>
                    <input type="file" name="file" required accept=".pdf,.docx,.doc,.xlsx,.xls,.csv,.png,.jpg,.jpeg"
                        class="text-[12px] text-brand-black file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0
                              file:text-[11px] file:font-medium file:bg-brand-bg file:text-brand-black
                              hover:file:bg-brand-border cursor-pointer">
                </div>
                <div class="flex flex-col gap-1 flex-1 min-w-[180px]">
                    <label class="text-[10px] text-brand-muted font-medium uppercase tracking-wide">Description
                        (optional)</label>
                    <input type="text" name="description" maxlength="500" placeholder="e.g. April occupancy report"
                        class="input h-9 w-full">
                </div>
                <button type="submit" class="btn-primary h-9">
                    <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload & Extract
                </button>
            </form>
            @error('file')
                <p class="text-[11px] text-status-red mt-2">{{ $message }}</p>
            @enderror
            @error('department_id')
                <p class="text-[11px] text-status-red mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Documents table --}}
        <div class="card overflow-hidden p-0">
            <div class="grid grid-cols-12 gap-3 table-header">
                <div class="col-span-4">Document</div>
                <div class="col-span-2 hidden sm:block">Department</div>
                <div class="col-span-2 hidden md:block">Uploaded By</div>
                <div class="col-span-2 hidden md:block">Date</div>
                <div class="col-span-1">AI Status</div>
                <div class="col-span-1">Actions</div>
            </div>

            @forelse($documents as $doc)
                @php
                    $statusColors = [
                        'pending' => 'text-status-orange bg-status-orange-bg',
                        'processing' => 'text-status-blue bg-status-blue-bg',
                        'extracted' => 'text-status-blue bg-status-blue-bg',
                        'review' => 'text-status-amber bg-status-amber-bg',
                        'confirmed' => 'text-status-green bg-status-green-bg',
                        'failed' => 'text-status-red bg-status-red-bg',
                    ];
                    $statusColor = $statusColors[$doc->ai_status] ?? 'text-brand-muted bg-brand-bg';
                @endphp
                <div class="data-row group grid grid-cols-12 gap-3">
                    {{-- Name --}}
                    <div class="col-span-4 flex items-center gap-2 min-w-0">
                        <div
                            class="w-7 h-7 rounded bg-brand-bg border border-brand-border flex items-center justify-center flex-shrink-0">
                            <span class="text-[8px] font-bold text-brand-muted">{{ $doc->file_type }}</span>
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('documents.show', $doc) }}"
                                class="text-[12px] font-medium text-brand-black hover:underline truncate block">
                                {{ $doc->original_name }}
                            </a>
                            <span class="text-[10px] text-brand-subtle">{{ $doc->size_for_humans }}</span>
                        </div>
                    </div>

                    {{-- Department --}}
                    <div class="col-span-2 hidden sm:flex items-center">
                        <span class="text-[11px] text-brand-muted">{{ $doc->department?->label ?? '—' }}</span>
                    </div>

                    {{-- Uploaded by --}}
                    <div class="col-span-2 hidden md:flex items-center">
                        <span class="text-[11px] text-brand-muted">{{ $doc->uploadedBy?->full_name ?? '—' }}</span>
                    </div>

                    {{-- Date --}}
                    <div class="col-span-2 hidden md:flex items-center">
                        <span class="text-[11px] text-brand-muted">{{ $doc->created_at->format('d M Y') }}</span>
                    </div>

                    {{-- AI Status --}}
                    <div class="col-span-1 flex items-center">
                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded uppercase {{ $statusColor }}">
                            {{ $doc->ai_status }}
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="col-span-1 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('documents.show', $doc) }}"
                            class="p-1 text-brand-muted hover:text-brand-black transition-colors" title="View">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <a href="{{ route('documents.download', $doc) }}"
                            class="p-1 text-brand-muted hover:text-brand-black transition-colors" title="Download">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('documents.destroy', $doc) }}"
                            data-remove-closest=".data-row">
                            @csrf @method('DELETE')
                            <button type="submit" data-confirm="This document will be permanently deleted."
                                class="p-1 text-brand-muted hover:text-status-red transition-colors" title="Delete">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-[12px] text-brand-muted">
                    No documents uploaded yet.
                </div>
            @endforelse
        </div>
        {{ $documents->links('components.pagination') }}
    </div>
@endsection
