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
        <div class="inline-flex items-center justify-center w-12 h-12 bg-brand-black rounded-xl mb-4">
            <span class="text-white font-bold text-sm">S&M</span>
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
