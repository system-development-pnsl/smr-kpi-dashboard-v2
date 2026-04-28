<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Sun & Moon Riverside Hotel</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="h-full overflow-hidden">

    <div class="flex h-screen w-screen overflow-hidden bg-brand-bg">

        {{-- Mobile backdrop --}}
        <div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

        {{-- ── Sidebar ────────────────────────────────────────────── --}}
        <aside id="sidebar" style="transition: transform 0.3s ease, width 0.3s ease;"
            class="flex flex-col h-screen bg-brand-black text-white flex-shrink-0
               fixed inset-y-0 left-0 z-50 w-[220px]
               md:relative md:translate-x-0">
            {{-- Logo --}}
            <div id="sidebar-logo" class="flex items-center border-b border-white/10 h-14 flex-shrink-0 px-4 gap-2.5">
                <div class="w-7 h-7 bg-white rounded flex items-center justify-center flex-shrink-0">
                    <span class="text-brand-black font-bold text-[10px] leading-none">S&M</span>
                </div>
                <div class="sidebar-expanded overflow-hidden">
                    <p class="text-[11px] font-semibold leading-tight tracking-wide whitespace-nowrap">SUN & MOON</p>
                    <p class="text-[9px] text-white/40 tracking-widest uppercase whitespace-nowrap">Riverside Hotel</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-3 overflow-y-auto overflow-x-hidden">
                <p class="sidebar-expanded px-4 mb-2 text-[9px] font-semibold tracking-widest text-white/30 uppercase">
                    Navigation
                </p>
                <ul class="space-y-0.5 px-2">
                    @php
                        $navItems = [
                            ['route' => 'dashboard', 'label' => 'Overview', 'icon' => 'grid'],
                            ['route' => 'kpi.index', 'label' => 'KPI Dashboard', 'icon' => 'chart-bar'],
                            ['route' => 'tasks.index', 'label' => 'Task Management', 'icon' => 'check-square'],
                            ['route' => 'financial.index', 'label' => 'Financial', 'icon' => 'dollar-sign'],
                            ['route' => 'documents.index', 'label' => 'Documents & AI', 'icon' => 'file-search'],
                            ['route' => 'reports.index', 'label' => 'Reports', 'icon' => 'file-text'],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                                class="sidebar-link {{ request()->routeIs($item['route'] . '*') ? 'active' : '' }} h-9 px-3"
                                data-sidebar-link>
                                @include('components.icons.' . $item['icon'], [
                                    'class' => 'w-[15px] h-[15px] flex-shrink-0',
                                ])
                                <span class="sidebar-expanded truncate">{{ $item['label'] }}</span>
                                @if (request()->routeIs($item['route'] . '*'))
                                    <svg class="sidebar-expanded ml-auto w-3 h-3 opacity-60" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- User area --}}
            <div class="border-t border-white/10 flex-shrink-0">
                {{-- Notifications --}}
                <div class="px-2 py-2">
                    <a href="{{ route('notifications.index') }}" id="sidebar-notif-link"
                        class="sidebar-link relative h-9 px-3 w-full">
                        @include('components.icons.bell', ['class' => 'w-[15px] h-[15px] flex-shrink-0'])
                        <span class="sidebar-expanded">Notifications</span>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute top-1.5 right-1.5 w-3.5 h-3.5 bg-status-red rounded-full text-[8px] font-bold flex items-center justify-center text-white">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
                </div>

                {{-- User profile — expanded --}}
                <div class="sidebar-expanded px-3 py-3 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-[11px] font-semibold">
                            {{ collect(explode(' ', auth()->user()->full_name))->map(fn($n) => $n[0])->take(2)->join('') }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-medium truncate">{{ auth()->user()->full_name }}</p>
                        <p class="text-[10px] text-white/40 truncate capitalize">
                            {{ str_replace('_', ' ', auth()->user()->role) }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white/30 hover:text-white/70 transition-colors"
                            title="Logout">
                            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- User profile — collapsed --}}
                <div class="sidebar-collapsed py-3 flex justify-center" style="display:none">
                    <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="text-[11px] font-semibold">
                            {{ collect(explode(' ', auth()->user()->full_name))->map(fn($n) => $n[0])->take(2)->join('') }}
                        </span>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ── Main content ───────────────────────────────────────── --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

            {{-- Top Header --}}
            <header
                class="h-14 bg-brand-surface border-b border-brand-border flex items-center px-4 gap-4 flex-shrink-0">
                {{-- Toggle --}}
                <button id="sidebar-toggle"
                    class="text-brand-muted hover:text-brand-black transition-colors p-1 rounded hover:bg-brand-bg">
                    <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Page title --}}
                <div class="flex-1 min-w-0">
                    <h1 class="text-[15px] font-semibold text-brand-black leading-tight">@yield('page-title', 'Operations Overview')</h1>
                    <p class="text-[11px] text-brand-muted hidden sm:block">@yield('page-sub', 'Sun & Moon Riverside Hotel')</p>
                </div>

                {{-- Search --}}
                <form action="{{ route('search') }}" method="GET"
                    class="hidden md:flex items-center gap-2 bg-brand-bg border border-brand-border rounded-lg px-3 h-8 w-44 focus-within:border-brand-black focus-within:w-56 transition-all duration-200">
                    <svg class="w-[13px] h-[13px] text-brand-subtle flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                    <input type="text" name="q" placeholder="Search tasks, KPIs…"
                        class="bg-transparent text-[12px] text-brand-black placeholder:text-brand-subtle outline-none w-full">
                </form>

                {{-- Date --}}
                <span class="hidden lg:block text-[11px] text-brand-muted whitespace-nowrap">
                    {{ now()->format('l, d F Y') }}
                </span>

                {{-- New Task --}}
                <a href="{{ route('tasks.create') }}" class="btn-primary">
                    <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">New Task</span>
                </a>
            </header>


            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto overflow-x-hidden p-5">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ── Global loading overlay ─────────────────────────────── --}}
    <div id="global-loading"
         style="display:none;position:fixed;inset:0;z-index:9999;background:#fff;
                flex-direction:column;align-items:center;justify-content:center;gap:20px;">
        <div style="width:48px;height:48px;border:3px solid #e5e5e3;
                    border-top-color:#0a0a0a;border-radius:50%;
                    animation:spin 0.8s linear infinite;"></div>
        <div style="text-align:center;">
            <p id="global-loading-msg"
               style="font-size:15px;font-weight:600;color:#0a0a0a;margin:0 0 6px;">
                Processing…
            </p>
            <p style="font-size:12px;color:#737373;margin:0;">Please don't close this page.</p>
        </div>
    </div>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key'))
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            window.__PUSHER_KEY__ = @json(config('broadcasting.connections.pusher.key'));
            window.__PUSHER_CLUSTER__ = @json(config('broadcasting.connections.pusher.options.cluster', 'ap1'));
        </script>
    @endif
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (t) => {
                t.onmouseenter = Swal.stopTimer;
                t.onmouseleave = Swal.resumeTimer;
            }
        });

        window.showLoading = function (msg) {
            document.getElementById('global-loading-msg').textContent = msg || 'Processing…';
            document.getElementById('global-loading').style.display = 'flex';
        };
        window.hideLoading = function () {
            document.getElementById('global-loading').style.display = 'none';
        };

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: @json(session('success'))
            });
        @endif
        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: @json(session('error'))
            });
        @endif
        @if (session('warning'))
            Toast.fire({
                icon: 'warning',
                title: @json(session('warning'))
            });
        @endif
        @if (session('info'))
            Toast.fire({
                icon: 'info',
                title: @json(session('info'))
            });
        @endif

        document.addEventListener('submit', async function(e) {
            const form = e.target;
            if (form.method.toLowerCase() === 'get') return;
            if ('noAjax' in form.dataset) return;

            e.preventDefault();

            const confirmBtn = form.querySelector('[data-confirm]');
            if (confirmBtn) {
                const { isConfirmed } = await Swal.fire({
                    title: 'Are you sure?',
                    text: confirmBtn.dataset.confirm,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#111827',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                });
                if (!isConfirmed) return;
            }

            const loadingMsg = form.dataset.loadingMessage;
            if (loadingMsg) window.showLoading(loadingMsg);

            const btns = [...form.querySelectorAll('[type="submit"]')];
            const originals = btns.map(b => b.innerHTML);
            btns.forEach(b => {
                b.disabled = true;
                b.innerHTML = '<span style="opacity:.5">…</span>';
            });

            let redirecting = false;

            try {
                const res = await fetch(form.action || window.location.href, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: new FormData(form),
                });

                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && data.errors) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: '<ul style="text-align:left;font-size:13px;line-height:1.6">' +
                                Object.values(data.errors).flat().map(m => `<li>• ${m}</li>`).join('') +
                                '</ul>',
                            confirmButtonColor: '#111827',
                        });
                    } else {
                        Toast.fire({ icon: 'error', title: data.message || 'Something went wrong.' });
                    }
                    return;
                }

                const removeSelector = form.dataset.removeClosest;
                if (removeSelector) {
                    const el = form.closest(removeSelector);
                    if (el) {
                        el.style.transition = 'opacity .15s';
                        el.style.opacity = '0';
                        setTimeout(() => el.remove(), 150);
                    }
                }

                if (data.redirect) {
                    redirecting = true;
                    if (loadingMsg) {
                        window.showLoading('Done — loading results…');
                    } else {
                        Toast.fire({ icon: 'success', title: data.message || 'Done' });
                    }
                    setTimeout(() => { window.location.href = data.redirect; }, loadingMsg ? 400 : 1200);
                } else {
                    Toast.fire({ icon: 'success', title: data.message || 'Done' });
                    if (!removeSelector) {
                        form.reset();
                        form.dispatchEvent(new CustomEvent('ajax:success', { detail: data, bubbles: true }));
                    }
                }

            } catch {
                Toast.fire({ icon: 'error', title: 'Network error. Please try again.' });
            } finally {
                if (!redirecting) {
                    if (loadingMsg) window.hideLoading();
                    btns.forEach((b, i) => { b.disabled = false; b.innerHTML = originals[i]; });
                }
            }
        });
    </script>
    <script>
        $(function() {
            $('[data-collapse-toggle]').each(function() {
                var id = $(this).data('collapse-toggle');
                if (localStorage.getItem('sec:' + id) === '0') {
                    $('#' + id).hide();
                    $(this).find('[data-chevron]').addClass('rotate-180');
                }
            });
            $(document).on('click', '[data-collapse-toggle]', function() {
                var id = $(this).data('collapse-toggle');
                var $el = $('#' + id);
                var $ico = $(this).find('[data-chevron]');
                if ($el.is(':visible')) {
                    $el.slideUp(220);
                    $ico.addClass('rotate-180');
                    localStorage.setItem('sec:' + id, '0');
                } else {
                    $el.slideDown(220);
                    $ico.removeClass('rotate-180');
                    localStorage.setItem('sec:' + id, '1');
                }
            });
        });
    </script>
    @stack('scripts')

</body>

</html>
