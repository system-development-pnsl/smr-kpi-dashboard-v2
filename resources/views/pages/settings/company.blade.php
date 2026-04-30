@extends('layouts.app')
@section('title', 'Company Settings')
@section('page-title', 'Company Settings')
@section('page-sub', 'Hotel profile, operations, and online presence')

@section('content')
<div class="max-w-5xl space-y-6">

    <form method="POST" action="{{ route('settings.company.update') }}">
        @csrf @method('PUT')

        {{-- ── 1. Hotel Identity ───────────────────────────────── --}}
        <div class="card space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-brand-border">
                {{-- Logo upload --}}
                <form method="POST" action="{{ route('settings.company.logo') }}"
                      enctype="multipart/form-data" data-no-ajax class="flex-shrink-0">
                    @csrf
                    <label title="Upload logo"
                           class="relative group block w-14 h-14 rounded-xl overflow-hidden
                                  border-2 border-dashed border-brand-border hover:border-brand-black
                                  bg-brand-bg cursor-pointer transition-colors flex-shrink-0">
                        @if($setting->logo)
                            <img src="{{ Storage::url($setting->logo) }}" alt="Logo"
                                 class="w-full h-full object-contain p-1">
                        @else
                            <img src="https://smr-zone.b-cdn.net/wp-content/uploads/2025/09/sun-and-moon-river-side-logo.png"
                                 alt="Logo" style="width:100%;height:100%;object-fit:contain;padding:4px;display:block;">
                        @endif
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center
                                    opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <input type="file" name="logo" accept="image/*" class="hidden"
                               onchange="this.closest('form').submit()">
                    </label>
                </form>

                <div>
                    <h3 class="text-[13px] font-semibold text-brand-black">Hotel Identity</h3>
                    <p class="text-[11px] text-brand-subtle mt-0.5">Name, branding, and basic info</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Hotel Name (English) <span class="text-status-red">*</span></label>
                    <input type="text" name="hotel_name" value="{{ old('hotel_name', $setting->hotel_name) }}"
                           class="input" required>
                    @error('hotel_name')<p class="text-[11px] text-status-red mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">Hotel Name (Khmer)</label>
                    <input type="text" name="hotel_name_km" value="{{ old('hotel_name_km', $setting->hotel_name_km) }}"
                           class="input" placeholder="ផ្ទះសំណាក់...">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Tagline / Slogan</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $setting->tagline) }}"
                           class="input" placeholder="e.g. Where Comfort Meets the River">
                </div>
                <div>
                    <label class="label">Star Rating</label>
                    <x-select-search
                        name="star_rating"
                        :options="collect(range(1,5))->map(fn($s) => ['value' => $s, 'label' => $s.' Star'.($s > 1 ? 's' : '')])->all()"
                        :selected="old('star_rating', $setting->star_rating)"
                        placeholder="Select rating…"
                        :required="true"
                    />
                </div>
                <div>
                    <label class="label">Established Year</label>
                    <x-select-search
                        name="established_year"
                        :options="collect(range(date('Y'), 1950, -1))->map(fn($y) => ['value' => $y, 'label' => (string)$y])->all()"
                        :selected="old('established_year', $setting->established_year)"
                        placeholder="Select year…"
                    />
                </div>
            </div>
        </div>

        {{-- ── 2. Contact & Location ────────────────────────────── --}}
        <div class="card space-y-5">
            <div class="pb-3 border-b border-brand-border">
                <h3 class="text-[13px] font-semibold text-brand-black">Contact & Location</h3>
                <p class="text-[11px] text-brand-subtle mt-0.5">Address, phone, email, and website</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="label">Street Address</label>
                    <input type="text" name="address" value="{{ old('address', $setting->address) }}"
                           class="input" placeholder="Street No, Village, Sangkat, Khan...">
                </div>
                <div>
                    <label class="label">City / Province</label>
                    <input type="text" name="city" value="{{ old('city', $setting->city) }}"
                           class="input" placeholder="Phnom Penh">
                </div>
                <div>
                    <label class="label">Country</label>
                    <input type="text" name="country" value="{{ old('country', $setting->country) }}"
                           class="input" placeholder="Cambodia">
                </div>
                <div>
                    <label class="label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}"
                           class="input" placeholder="+855 23 000 000">
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $setting->email) }}"
                           class="input" placeholder="info@hotel.com">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Website</label>
                    <input type="url" name="website" value="{{ old('website', $setting->website) }}"
                           class="input" placeholder="https://www.hotel.com">
                </div>
            </div>
        </div>

        {{-- ── 3. Operations ────────────────────────────────────── --}}
        <div class="card space-y-5">
            <div class="pb-3 border-b border-brand-border">
                <h3 class="text-[13px] font-semibold text-brand-black">Operations</h3>
                <p class="text-[11px] text-brand-subtle mt-0.5">Check-in/out, currency, tax, and capacity</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="label">Check-in Time</label>
                    <x-select-search
                        name="checkin_time"
                        :options="collect(['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00'])
                            ->map(fn($t) => ['value' => $t.':00', 'label' => \Carbon\Carbon::createFromFormat('H:i', $t)->format('g:i A')])->all()"
                        :selected="old('checkin_time', substr($setting->checkin_time, 0, 5).':00')"
                        placeholder="Select time…"
                        :required="true"
                    />
                </div>
                <div>
                    <label class="label">Check-out Time</label>
                    <x-select-search
                        name="checkout_time"
                        :options="collect(['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00'])
                            ->map(fn($t) => ['value' => $t.':00', 'label' => \Carbon\Carbon::createFromFormat('H:i', $t)->format('g:i A')])->all()"
                        :selected="old('checkout_time', substr($setting->checkout_time, 0, 5).':00')"
                        placeholder="Select time…"
                        :required="true"
                    />
                </div>
                <div>
                    <label class="label">Total Rooms</label>
                    <input type="number" name="total_rooms"
                           value="{{ old('total_rooms', $setting->total_rooms) }}"
                           class="input" min="0" placeholder="0">
                </div>
                <div>
                    <label class="label">Currency</label>
                    <x-select-search
                        name="currency"
                        :options="[
                            ['value' => 'USD', 'label' => 'USD — US Dollar'],
                            ['value' => 'KHR', 'label' => 'KHR — Khmer Riel'],
                            ['value' => 'THB', 'label' => 'THB — Thai Baht'],
                            ['value' => 'EUR', 'label' => 'EUR — Euro'],
                            ['value' => 'SGD', 'label' => 'SGD — Singapore Dollar'],
                        ]"
                        :selected="old('currency', $setting->currency)"
                        placeholder="Select currency…"
                        :required="true"
                    />
                </div>
                <div>
                    <label class="label">Timezone</label>
                    <x-select-search
                        name="timezone"
                        :options="[
                            ['value' => 'Asia/Phnom_Penh',  'label' => 'Phnom Penh (UTC+7)'],
                            ['value' => 'Asia/Bangkok',      'label' => 'Bangkok (UTC+7)'],
                            ['value' => 'Asia/Singapore',    'label' => 'Singapore (UTC+8)'],
                            ['value' => 'Asia/Ho_Chi_Minh',  'label' => 'Ho Chi Minh (UTC+7)'],
                            ['value' => 'Asia/Kuala_Lumpur', 'label' => 'Kuala Lumpur (UTC+8)'],
                            ['value' => 'Asia/Tokyo',        'label' => 'Tokyo (UTC+9)'],
                            ['value' => 'UTC',               'label' => 'UTC'],
                        ]"
                        :selected="old('timezone', $setting->timezone)"
                        placeholder="Select timezone…"
                        :required="true"
                    />
                </div>
                <div>
                    <label class="label">VAT Rate (%)</label>
                    <input type="number" name="vat_rate"
                           value="{{ old('vat_rate', $setting->vat_rate) }}"
                           class="input" step="0.01" min="0" max="100" placeholder="10">
                </div>
            </div>
        </div>

        {{-- ── 4. Social & Online Presence ─────────────────────── --}}
        <div class="card space-y-5">
            <div class="pb-3 border-b border-brand-border">
                <h3 class="text-[13px] font-semibold text-brand-black">Social & Online Presence</h3>
                <p class="text-[11px] text-brand-subtle mt-0.5">Links shown on guest-facing materials</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['facebook',    'Facebook Page',   'https://facebook.com/yourhotel'],
                    ['instagram',   'Instagram',       'https://instagram.com/yourhotel'],
                    ['tripadvisor', 'TripAdvisor',     'https://tripadvisor.com/...'],
                    ['booking_com', 'Booking.com',     'https://booking.com/...'],
                ] as [$field, $label, $ph])
                <div>
                    <label class="label">{{ $label }}</label>
                    <input type="url" name="{{ $field }}"
                           value="{{ old($field, $setting->$field) }}"
                           class="input" placeholder="{{ $ph }}">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Save bar --}}
        <div class="flex items-center justify-between card py-3">
            <p class="text-[12px] text-brand-subtle">Changes apply immediately across the system.</p>
            <button type="submit" class="btn-primary px-6">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Settings
            </button>
        </div>

    </form>
</div>
@endsection
