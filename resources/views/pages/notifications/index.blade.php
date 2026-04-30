@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-sub', 'Your alerts and system messages')

@section('content')
@php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp

<div class="space-y-5">

    {{-- Header bar --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="text-[13px] font-semibold text-brand-black">All Notifications</span>
            @if($unreadCount > 0)
                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full
                             bg-status-blue text-white text-[10px] font-bold">
                    {{ $unreadCount }}
                </span>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <span class="text-[11px] text-brand-subtle">
                {{ $notifications->total() }} total
            </span>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf @method('PUT')
                    <button type="submit" class="btn-secondary h-8 gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Notification cards --}}
    <div class="space-y-2">
        @forelse($notifications as $notification)
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
                        => ['bg-brand-bg',        'text-brand-muted',
                            'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                };
            @endphp

            <div class="group relative flex items-center gap-4 bg-brand-surface border rounded-xl px-6 py-5
                        hover:shadow-card transition-all duration-200
                        {{ $isUnread
                            ? 'border-brand-border border-l-[3px] border-l-status-blue shadow-sm'
                            : 'border-brand-border/60' }}">

                {{-- Icon avatar --}}
                <div class="flex-shrink-0 ml-2 w-11 h-11 rounded-xl flex items-center justify-center {{ $iconBg }} {{ $iconColor }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $iconPath }}"/>
                    </svg>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] leading-snug
                              {{ $isUnread ? 'font-semibold text-brand-black' : 'text-brand-muted' }}">
                        {{ $notification->data['message'] ?? 'Notification' }}
                    </p>

                    @if(!empty($notification->data['body']))
                        <p class="text-[12px] text-brand-subtle mt-1 leading-relaxed">
                            {{ $notification->data['body'] }}
                        </p>
                    @endif

                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[11px] text-brand-subtle">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                        @if($isUnread)
                            <span class="w-1 h-1 rounded-full bg-brand-border inline-block"></span>
                            <span class="text-[10px] font-semibold text-status-blue uppercase tracking-wider">New</span>
                        @endif
                    </div>
                </div>

                {{-- Actions (hover) --}}
                <div class="flex-shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity self-center">
                    @if($isUnread)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf @method('PUT')
                            <button type="submit"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg
                                           text-brand-subtle hover:text-status-green hover:bg-status-green-bg
                                           transition-colors"
                                    title="Mark as read">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}"
                          data-remove-closest=".group">
                        @csrf @method('DELETE')
                        <button type="submit"
                                data-confirm="This notification will be deleted."
                                class="w-7 h-7 flex items-center justify-center rounded-lg
                                       text-brand-subtle hover:text-status-red hover:bg-status-red-bg
                                       transition-colors"
                                title="Delete">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

        @empty
            {{-- Empty state --}}
            <div class="card flex flex-col items-center justify-center py-20 text-center">
                <div class="w-14 h-14 rounded-2xl bg-brand-bg flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-brand-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-[13px] font-medium text-brand-black mb-1">You're all caught up</p>
                <p class="text-[12px] text-brand-subtle">No notifications to show right now.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    {{ $notifications->links('components.pagination') }}

</div>
@endsection
