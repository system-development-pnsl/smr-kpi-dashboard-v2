@extends('layouts.app')
@section('title', 'User Setting')
@section('page-title', 'User Setting')
@section('page-sub', 'Manage your profile, security and preferences')

@section('content')
<div class="space-y-6 max-w-5xl">

    {{-- ── Profile Hero ───────────────────────────────────────── --}}
    <div class="card p-0 overflow-hidden" style="display:flex;flex-direction:row;">

        {{-- Left dark panel --}}
        <div class="flex-shrink-0 flex flex-col items-center justify-center gap-3 px-8 py-8"
             style="background:#0a0a0a;min-width:200px;">

            {{-- Circle avatar --}}
            <div class="relative" style="width:100px;height:100px;">
                <div style="width:100px;height:100px;border-radius:9999px;overflow:hidden;
                            border:3px solid rgba(255,255,255,0.15);">
                    @if($user->profile_photo)
                        <img src="{{ Storage::url($user->profile_photo) }}"
                             alt="{{ $user->full_name }}"
                             style="width:100px;height:100px;object-fit:cover;display:block;">
                    @else
                        <div style="width:100px;height:100px;background:rgba(255,255,255,0.1);
                                    display:flex;align-items:center;justify-content:center;">
                            <span style="color:#fff;font-size:30px;font-weight:700;line-height:1;">
                                {{ $user->initials }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Upload button --}}
                <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" data-no-ajax>
                    @csrf
                    <label title="Change photo"
                           style="position:absolute;bottom:2px;right:2px;width:26px;height:26px;
                                  border-radius:9999px;background:#fff;display:flex;
                                  align-items:center;justify-content:center;cursor:pointer;
                                  box-shadow:0 1px 4px rgba(0,0,0,.3);">
                        <svg style="width:12px;height:12px;color:#0a0a0a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0"/>
                        </svg>
                        <input type="file" name="photo" accept="image/*" class="hidden"
                               onchange="this.closest('form').submit()">
                    </label>
                </form>
            </div>

            {{-- Employee code --}}
            <span class="text-[11px] font-mono text-white/40">{{ $user->code }}</span>
        </div>

        {{-- Right info panel --}}
        <div class="flex-1 min-w-0" style="padding:24px 32px;">

            {{-- Top row: name + status --}}
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-[18px] font-bold text-brand-black leading-tight truncate">
                        {{ $user->full_name }}
                    </h2>
                    @if($user->full_name_km)
                        <p class="text-[12px] text-brand-subtle mt-0.5">{{ $user->full_name_km }}</p>
                    @endif
                </div>
                <span class="flex-shrink-0 {{ $user->status === 'active' ? 'badge-green' : 'badge-red' }}">
                    {{ ucfirst($user->status) }}
                </span>
            </div>

            {{-- Role + dept --}}
            <p class="text-[13px] text-brand-muted mt-1.5">
                {{ $user->job_title }}
                @if($user->department && $user->department->label !== $user->job_title)
                    <span class="text-brand-border mx-1">&middot;</span>
                    {{ $user->department->label }}
                @endif
            </p>

            {{-- Divider --}}
            <div class="border-t border-brand-border my-4"></div>

            {{-- Meta row --}}
            <div class="flex flex-wrap gap-x-6 gap-y-2">
                @if($user->start_date)
                <span class="flex items-center gap-1.5 text-[12px] text-brand-muted">
                    <svg class="w-3.5 h-3.5 text-brand-subtle flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Since {{ $user->start_date->format('M Y') }}
                </span>
                @endif
                <span class="flex items-center gap-1.5 text-[12px] text-brand-muted">
                    <svg class="w-3.5 h-3.5 text-brand-subtle flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $user->email }}
                </span>
                @if($user->phone)
                <span class="flex items-center gap-1.5 text-[12px] text-brand-muted">
                    <svg class="w-3.5 h-3.5 text-brand-subtle flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $user->phone }}
                </span>
                @endif
                <span class="flex items-center gap-1.5 text-[12px] text-brand-muted capitalize">
                    <svg class="w-3.5 h-3.5 text-brand-subtle flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ str_replace('_', ' ', $user->employment_type ?? 'Full time') }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left column ─────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Edit Profile --}}
            <div class="card">
                <h3 class="text-[13px] font-semibold text-brand-black mb-4">Personal Information</h3>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Full Name (EN)</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                                   class="input" required>
                            @error('full_name')
                                <p class="text-[11px] text-status-red mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                   placeholder="+855 12 345 678" class="input">
                            @error('phone')
                                <p class="text-[11px] text-status-red mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="label">Email</label>
                            <input type="email" value="{{ $user->email }}" class="input bg-brand-bg" disabled>
                        </div>

                        <div>
                            <label class="label">Employee Code</label>
                            <input type="text" value="{{ $user->code }}" class="input bg-brand-bg" disabled>
                        </div>

                        <div>
                            <label class="label">Job Title</label>
                            <input type="text" value="{{ $user->job_title }}" class="input bg-brand-bg" disabled>
                        </div>

                        <div>
                            <label class="label">Department</label>
                            <input type="text" value="{{ $user->department?->label ?? '—' }}" class="input bg-brand-bg" disabled>
                        </div>

                        <div>
                            <label class="label">Gender</label>
                            <input type="text" value="{{ ucfirst($user->gender ?? '—') }}" class="input bg-brand-bg" disabled>
                        </div>

                        <div>
                            <label class="label">Start Date</label>
                            <input type="text" value="{{ $user->start_date?->format('d M Y') ?? '—' }}" class="input bg-brand-bg" disabled>
                        </div>
                    </div>

                    <div class="flex justify-end mt-5 pt-4 border-t border-brand-border">
                        <button type="submit" class="btn-primary">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="card">
                <h3 class="text-[13px] font-semibold text-brand-black mb-1">Change Password</h3>
                <p class="text-[11px] text-brand-subtle mb-4">Minimum 10 characters. Use a mix of letters, numbers, and symbols.</p>

                <form method="POST" action="{{ route('password.change') }}">
                    @csrf @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="label">Current Password</label>
                            <input type="password" name="current_password" class="input" required>
                            @error('current_password')
                                <p class="text-[11px] text-status-red mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="label">New Password</label>
                                <input type="password" name="password" class="input" required>
                                @error('password')
                                    <p class="text-[11px] text-status-red mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="input" required>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-5 pt-4 border-t border-brand-border">
                        <button type="submit" class="btn-primary">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>

        {{-- ── Right column ─────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Account overview --}}
            <div class="card">
                <h3 class="text-[13px] font-semibold text-brand-black mb-4">Account Overview</h3>
                <dl class="space-y-3">
                    <div class="flex items-center justify-between">
                        <dt class="text-[11px] text-brand-muted">Role</dt>
                        <dd class="text-[12px] font-medium text-brand-black capitalize">
                            {{ str_replace('_', ' ', $user->role) }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-[11px] text-brand-muted">Employment</dt>
                        <dd class="text-[12px] font-medium text-brand-black capitalize">
                            {{ str_replace('_', ' ', $user->employment_type ?? '—') }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-[11px] text-brand-muted">Schedule</dt>
                        <dd class="text-[12px] font-medium text-brand-black capitalize">
                            {{ str_replace('_', ' ', $user->work_schedule ?? '—') }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-[11px] text-brand-muted">Reporting To</dt>
                        <dd class="text-[12px] font-medium text-brand-black truncate max-w-[120px]">
                            {{ $user->manager?->full_name ?? '—' }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Access modules --}}
            <div class="card">
                <h3 class="text-[13px] font-semibold text-brand-black mb-3">Module Access</h3>
                <div class="flex flex-wrap gap-1.5">
                    @forelse((array) $user->access_modules as $module)
                        <span class="badge-blue capitalize">{{ str_replace('_', ' ', $module) }}</span>
                    @empty
                        <span class="text-[12px] text-brand-subtle">No modules assigned.</span>
                    @endforelse
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="card border-status-red/30">
                <h3 class="text-[13px] font-semibold text-status-red mb-1">Sign Out</h3>
                <p class="text-[11px] text-brand-subtle mb-3">You will be logged out of all sessions on this device.</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full btn-danger justify-center">
                        Sign Out
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
