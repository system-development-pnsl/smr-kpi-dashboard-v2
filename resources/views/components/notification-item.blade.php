@php
    $isUnread = is_null($notification->read_at);
    $msg      = strtolower($notification->data['message'] ?? '');

    [$iconBg, $iconColor, $iconPath] = match(true) {
        str_contains($msg, 'kpi') || str_contains($msg, 'score')
            => ['bg-status-blue-bg',  'text-status-blue',
                'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],

        str_contains($msg, 'task') || str_contains($msg, 'assigned') || str_contains($msg, 'overdue')
            => ['bg-status-amber-bg', 'text-status-amber',
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],

        str_contains($msg, 'document') || str_contains($msg, 'upload') || str_contains($msg, 'file')
            => ['bg-status-green-bg', 'text-status-green',
                'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],

        str_contains($msg, 'revenue') || str_contains($msg, 'target') || str_contains($msg, 'exceeded')
            => ['bg-status-green-bg', 'text-status-green',
                'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],

        str_contains($msg, 'budget') || str_contains($msg, 'variance') || str_contains($msg, 'cost')
            => ['bg-status-red-bg',   'text-status-red',
                'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 13v-1m0-4c-1.657 0-3-.895-3-2s1.343-2 3-2m0 4c1.657 0 3 .895 3 2s-1.343 2-3 2'],

        str_contains($msg, 'action plan') || str_contains($msg, 'plan') || str_contains($msg, 'attention')
            => ['bg-status-amber-bg', 'text-status-amber',
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6m-6 4h4'],

        default
            => ['bg-brand-bg', 'text-brand-muted',
                'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
    };
@endphp

<div class="flex items-center gap-3 py-2 {{ $isUnread ? 'opacity-100' : 'opacity-60' }}">

    {{-- Icon --}}
    <div class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center {{ $iconBg }} {{ $iconColor }}">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $iconPath }}"/>
        </svg>
    </div>

    {{-- Text --}}
    <div class="flex-1 min-w-0">
        <p class="text-[12px] leading-snug truncate {{ $isUnread ? 'font-medium text-brand-black' : 'text-brand-muted' }}">
            {{ $notification->data['message'] ?? 'Notification' }}
        </p>
        <p class="text-[10px] text-brand-subtle mt-0.5">
            {{ $notification->created_at->diffForHumans() }}
        </p>
    </div>

    {{-- Unread dot --}}
    @if($isUnread)
        <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-status-blue"></div>
    @endif

</div>
