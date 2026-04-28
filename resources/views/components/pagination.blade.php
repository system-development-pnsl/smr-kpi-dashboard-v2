@if ($paginator->hasPages())
<div class="flex items-center justify-between mt-4 px-1">

    {{-- Results summary --}}
    <p class="text-[11px] text-brand-muted">
        Showing <span class="font-medium text-brand-black">{{ $paginator->firstItem() }}</span>
        to <span class="font-medium text-brand-black">{{ $paginator->lastItem() }}</span>
        of <span class="font-medium text-brand-black">{{ $paginator->total() }}</span> results
    </p>

    {{-- Page controls --}}
    <div class="flex items-center gap-1">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-7 h-7 rounded text-[11px]
                         text-brand-border cursor-not-allowed select-none">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="inline-flex items-center justify-center w-7 h-7 rounded text-[11px]
                      text-brand-muted hover:bg-brand-bg hover:text-brand-black transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="inline-flex items-center justify-center w-7 h-7 text-[11px] text-brand-subtle">
                    …
                </span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded text-[11px]
                                     font-semibold bg-brand-black text-white">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="inline-flex items-center justify-center w-7 h-7 rounded text-[11px]
                                  text-brand-muted hover:bg-brand-bg hover:text-brand-black transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="inline-flex items-center justify-center w-7 h-7 rounded text-[11px]
                      text-brand-muted hover:bg-brand-bg hover:text-brand-black transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-7 h-7 rounded text-[11px]
                         text-brand-border cursor-not-allowed select-none">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        @endif

    </div>
</div>
@endif
