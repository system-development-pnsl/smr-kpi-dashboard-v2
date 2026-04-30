<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — Sun & Moon Riverside Hotel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-brand-bg flex items-center justify-center p-4">

<div class="w-full max-w-[400px]">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div style="margin-bottom:16px;">
            <img src="https://smr-zone.b-cdn.net/wp-content/uploads/2025/09/sun-and-moon-river-side-logo.png"
                 alt="Sun & Moon Riverside Hotel"
                 style="width:80px;height:80px;object-fit:contain;display:block;margin:0 auto;">
        </div>
        <h1 class="text-[20px] font-semibold text-brand-black">Sun & Moon</h1>
        <p class="text-[12px] text-brand-muted mt-0.5">Riverside Hotel — Operations Dashboard</p>
    </div>

    {{-- Card --}}
    <div class="card">
        @yield('content')
    </div>

    <p class="text-center text-[11px] text-brand-subtle mt-6">
        &copy; {{ date('Y') }} Sun & Moon Riverside Hotel · Confidential
    </p>
</div>

</body>
</html>
