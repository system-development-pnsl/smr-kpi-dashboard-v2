@props([
    'name',
    'options'     => [],   // array of ['value' => ..., 'label' => ...]
    'selected'    => [],   // pre-selected values (array)
    'placeholder' => 'Select…',
    'required'    => false,
    'class'       => '',
])

@php
    $uid     = 'ssm_' . md5($name . uniqid());
    $initial = old(rtrim($name, '[]'), $selected) ?? [];
    $initial = array_map('strval', (array) $initial);

    $initLabels = collect($options)
        ->filter(fn($o) => in_array((string)$o['value'], $initial))
        ->pluck('label')
        ->values();

    if ($initLabels->count() === 1) {
        $initLabel = $initLabels->first();
    } elseif ($initLabels->count() > 1) {
        $initLabel = $initLabels->first() . ' +' . ($initLabels->count() - 1);
    } else {
        $initLabel = '';
    }
@endphp

<div id="{{ $uid }}"
     data-ssm-wrap
     data-placeholder="{{ $placeholder }}"
     data-field-name="{{ $name }}"
     class="relative {{ $class }}">

    {{-- Hidden inputs container --}}
    <div data-ssm-inputs>
        @forelse($initial as $val)
            <input type="hidden" name="{{ $name }}" value="{{ $val }}" data-ssm-hidden>
        @empty
            <input type="hidden" name="{{ $name }}" value="" data-ssm-fallback>
        @endforelse
    </div>

    {{-- Trigger button --}}
    <button type="button" data-ssm-trigger
            class="select h-9 w-full flex items-center justify-between gap-2 text-left {{ empty($initial) ? 'text-brand-subtle' : 'text-brand-black' }}">
        <span data-ssm-label class="truncate text-[12px]">{{ $initLabel ?: $placeholder }}</span>
        <div class="flex items-center gap-1.5 flex-shrink-0">
            <span data-ssm-count
                  class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-brand-black text-white"
                  @if(empty($initial)) style="display:none" @endif>{{ count($initial) ?: '' }}</span>
            <svg data-ssm-chevron
                 class="w-3.5 h-3.5 text-brand-muted transition-transform duration-150"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    {{-- Dropdown --}}
    <div data-ssm-dropdown
         class="absolute z-50 mt-1 w-full min-w-[220px] bg-brand-surface border border-brand-border rounded-lg shadow-lg overflow-hidden"
         style="display:none">

        {{-- Search + clear all --}}
        <div class="p-2 border-b border-brand-border flex items-center gap-2">
            <div class="flex items-center gap-2 bg-brand-bg border border-brand-border rounded px-2.5 h-7 flex-1">
                <svg class="w-3 h-3 text-brand-subtle flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" data-ssm-search placeholder="Search…"
                       class="bg-transparent text-[11px] text-brand-black placeholder:text-brand-subtle outline-none w-full">
            </div>
            <button type="button" data-ssm-clearall
                    @if(empty($initial)) style="display:none" @endif
                    class="text-[10px] text-brand-muted hover:text-status-red transition-colors whitespace-nowrap">
                Clear all
            </button>
        </div>

        {{-- Options --}}
        <ul class="max-h-52 overflow-y-auto py-1">
            @foreach($options as $opt)
            @php $isSelected = in_array((string)$opt['value'], $initial); @endphp
            <li>
                <button type="button"
                        data-ssm-option
                        data-value="{{ $opt['value'] }}"
                        data-label="{{ $opt['label'] }}"
                        class="w-full text-left px-3 py-1.5 flex items-center gap-2.5 transition-colors hover:bg-brand-bg">
                    <div data-ssm-cbwrap
                         class="w-3.5 h-3.5 rounded border flex-shrink-0 flex items-center justify-center transition-colors {{ $isSelected ? 'bg-brand-black border-brand-black' : 'border-brand-border bg-white' }}">
                        <svg data-ssm-checksvg
                             class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             @if(!$isSelected) style="display:none" @endif>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-[12px] text-brand-black">{{ $opt['label'] }}</span>
                </button>
            </li>
            @endforeach

            <li data-ssm-noresults class="px-3 py-3 text-[11px] text-brand-subtle text-center" style="display:none">
                No results
            </li>
        </ul>

        {{-- Footer count --}}
        <div data-ssm-footer
             class="px-3 py-2 border-t border-brand-border flex items-center justify-between"
             @if(empty($initial)) style="display:none" @endif>
            <span data-ssm-footercount class="text-[10px] text-brand-muted">{{ count($initial) }} selected</span>
            <button type="button" data-ssm-done
                    class="text-[11px] font-medium text-brand-black hover:underline">Done</button>
        </div>
    </div>
</div>
