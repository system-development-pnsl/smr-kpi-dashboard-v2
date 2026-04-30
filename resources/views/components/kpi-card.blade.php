{{-- resources/views/components/kpi-card.blade.php --}}
@php
    $isLowerBetter = $kpi->is_lower_better ?? false;
    $latestValue   = $kpi->latestEntry?->value ?? 0;
    $target        = $kpi->target ?? 1;
    $ratio         = $isLowerBetter ? $target / max($latestValue, 0.001) : $latestValue / max($target, 0.001);
    $pct           = min(round($ratio * 100), 100);
    $status        = $ratio >= 1.0 ? 'green' : ($ratio >= 0.8 ? 'amber' : 'red');
    $change        = $kpi->changeVsPrevious() ?? 0;
    $changePositive = $isLowerBetter ? $change <= 0 : $change >= 0;

    $statusLabels  = ['green' => 'On Track', 'amber' => 'Near Target', 'red' => 'Off Track'];
    $barColors     = ['green' => 'bg-status-green', 'amber' => 'bg-status-amber', 'red' => 'bg-status-red'];
    $badgeClasses  = ['green' => 'badge-green', 'amber' => 'badge-amber', 'red' => 'badge-red'];

    $displayValue = match($kpi->unit) {
        'USD'   => '$'.number_format($latestValue, $latestValue >= 1000 ? 0 : 2),
        '%'     => number_format($latestValue, 1).'%',
        default => number_format($latestValue, 1).' '.$kpi->unit,
    };
@endphp

<div class="card card-hover flex flex-col gap-3 relative" data-kpi-id="{{ $kpi->id }}">
    {{-- Top row --}}
    <div class="flex items-start justify-between gap-2">
        <div class="flex-1 min-w-0">
            <p class="text-[11px] font-medium text-brand-muted uppercase tracking-wide truncate">{{ $kpi->name }}</p>
            <p class="text-[22px] font-semibold text-brand-black leading-tight mt-0.5 tracking-tight">{{ $displayValue }}</p>
        </div>
        <div class="flex items-center gap-1.5 flex-shrink-0">
            <span class="{{ $badgeClasses[$status] }}">{{ $statusLabels[$status] }}</span>
            <button type="button" class="kpi-hide-btn w-5 h-5 flex items-center justify-center rounded
                    text-brand-subtle hover:text-brand-black hover:bg-brand-bg transition-colors"
                    style="opacity:0;" title="Hide card">
                <svg class="w-[11px] h-[11px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Sparkline --}}
    @if($kpi->history && count($kpi->history) > 0)
        <div class="h-10 -mx-1">
            <canvas id="spark-{{ $kpi->id }}" class="w-full h-full"></canvas>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                initSparkline('spark-{{ $kpi->id }}', @json($kpi->history), '{{ $status }}')
            })
        </script>
    @endif

    {{-- Progress bar --}}
    <div>
        <div class="flex items-center justify-between mb-1">
            <span class="text-[10px] text-brand-subtle">
                Target: {{ $kpi->unit === 'USD' ? '$'.number_format($target, 0) : number_format($target, 1).' '.$kpi->unit }}
            </span>
            <span class="text-[10px] font-semibold text-brand-muted">{{ $pct }}%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-bar-fill {{ $barColors[$status] }}" style="width: {{ $pct }}%"></div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="flex items-center justify-between pt-0.5">
        <span class="flex items-center gap-1 text-[11px] font-medium {{ $changePositive ? 'text-status-green' : 'text-status-red' }}">
            <svg class="w-[11px] h-[11px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="{{ $changePositive ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/>
            </svg>
            {{ $change > 0 ? '+' : '' }}{{ number_format($change, 1) }}
            <span class="text-brand-subtle font-normal ml-0.5">vs last month</span>
        </span>
        <span class="text-[10px] text-brand-subtle">{{ $kpi->update_frequency }}</span>
    </div>
</div>
