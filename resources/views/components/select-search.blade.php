@props([
    'name',
    'options'     => [],   // array of ['value' => ..., 'label' => ...]
    'selected'    => null, // pre-selected value
    'placeholder' => 'Select…',
    'required'    => false,
    'class'       => '',
    'formId'      => null,
])

@php
    $uid       = 'ss_' . md5($name . uniqid());
    $initial   = old($name, $selected);
    $initLabel = collect($options)->firstWhere('value', $initial)['label'] ?? null;
    $hasValue  = $initial !== null && $initial !== '';
@endphp

<div id="{{ $uid }}"
     data-ss-wrap
     data-placeholder="{{ $placeholder }}"
     @if($formId) data-form-id="{{ $formId }}" @endif
     class="relative {{ $class }}">

    {{-- Hidden real input for form submission --}}
    <input type="hidden" name="{{ $name }}" value="{{ $initial ?? '' }}" data-ss-input
           @if($required && !$hasValue) required @endif>

    {{-- Trigger button --}}
    <button type="button" data-ss-trigger
            class="select h-9 w-full flex items-center justify-between gap-2 text-left {{ $hasValue ? 'text-brand-black' : 'text-brand-subtle' }}">
        <span data-ss-label class="truncate text-[12px]">{{ $initLabel ?? $placeholder }}</span>
        <svg data-ss-chevron
             class="w-3.5 h-3.5 flex-shrink-0 text-brand-muted transition-transform duration-150"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Dropdown --}}
    <div data-ss-dropdown
         class="absolute z-50 mt-1 w-full min-w-[180px] bg-brand-surface border border-brand-border rounded-lg shadow-lg overflow-hidden"
         style="display:none">

        {{-- Search input --}}
        <div class="p-2 border-b border-brand-border">
            <div class="flex items-center gap-2 bg-brand-bg border border-brand-border rounded px-2.5 h-7">
                <svg class="w-3 h-3 text-brand-subtle flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" data-ss-search placeholder="Search…"
                       class="bg-transparent text-[11px] text-brand-black placeholder:text-brand-subtle outline-none w-full">
            </div>
        </div>

        {{-- Options list --}}
        <ul class="max-h-48 overflow-y-auto py-1">
            {{-- Clear / placeholder option --}}
            @unless($required)
            <li>
                <button type="button" data-ss-clear
                        class="w-full text-left px-3 py-1.5 text-[11px] text-brand-subtle hover:bg-brand-bg transition-colors">
                    {{ $placeholder }}
                </button>
            </li>
            @endunless

            @foreach($options as $opt)
            @php $isSelected = $hasValue && (string)$initial === (string)$opt['value']; @endphp
            <li>
                <button type="button"
                        data-ss-option
                        data-value="{{ $opt['value'] }}"
                        data-label="{{ $opt['label'] }}"
                        class="w-full text-left px-3 py-1.5 text-[12px] transition-colors hover:bg-brand-bg text-brand-black {{ $isSelected ? 'font-semibold bg-brand-bg' : '' }}">
                    {{ $opt['label'] }}
                    <svg data-ss-check
                         class="inline-block w-3 h-3 ml-1 text-brand-black"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         @if(!$isSelected) style="display:none" @endif>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </li>
            @endforeach

            <li data-ss-noresults class="px-3 py-3 text-[11px] text-brand-subtle text-center" style="display:none">
                No results
            </li>
        </ul>
    </div>
</div>
