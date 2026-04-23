@extends('layouts.auth')
@section('title', 'Login')

@section('content')
    <h2 class="text-[16px] font-semibold text-brand-black mb-1">Welcome back</h2>
    <p class="text-[12px] text-brand-muted mb-6">Sign in to your dashboard account</p>

    @if ($errors->any())
        <div class="mb-4 px-3 py-2.5 bg-status-red-bg border border-status-red/20 rounded-lg">
            <p class="text-[12px] text-status-red font-medium">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="label">Email Address</label>
            <input type="email" name="email" value="gm@sunmoon.hotel"
                class="input @error('email') border-status-red @enderror" placeholder="you@sunmoon.hotel" required
                autofocus>
        </div>

        <div>
            <label class="label">Password</label>
            <input type="password" name="password" class="input @error('password') border-status-red @enderror"
                placeholder="••••••••••" required value="Password@2026">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-brand-border text-brand-black">
                <span class="text-[12px] text-brand-muted">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}"
                class="text-[12px] text-brand-muted hover:text-brand-black transition-colors">
                Forgot password?
            </a>
        </div>

        <button type="submit" class="btn-primary w-full justify-center h-9">
            Sign In
        </button>
    </form>

    <div class="mt-6 pt-4 border-t border-brand-border">
        <p class="text-[11px] text-brand-subtle text-center mb-3">Demo accounts</p>
        <div class="space-y-1.5">
            @foreach ([['role' => 'General Manager', 'email' => 'gm@sunmoon.hotel'], ['role' => 'Finance Director', 'email' => 'finance@sunmoon.hotel'], ['role' => 'Front Office', 'email' => 'frontoffice@sunmoon.hotel']] as $demo)
                <div class="flex items-center justify-between px-2.5 py-1.5 bg-brand-bg rounded-lg">
                    <span class="text-[11px] text-brand-muted">{{ $demo['role'] }}</span>
                    <span class="text-[11px] font-mono text-brand-black">{{ $demo['email'] }}</span>
                </div>
            @endforeach
            <p class="text-[10px] text-brand-subtle text-center">Password: Password@2026</p>
        </div>
    </div>
@endsection
