<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanySettingController extends Controller
{
    public function index(): View
    {
        return view('pages.settings.company', ['setting' => CompanySetting::instance()]);
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'hotel_name'       => 'required|string|max:150',
            'hotel_name_km'    => 'nullable|string|max:150',
            'tagline'          => 'nullable|string|max:255',
            'star_rating'      => 'required|integer|min:1|max:5',
            'established_year' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'address'          => 'nullable|string|max:500',
            'city'             => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
            'phone'            => 'nullable|string|max:30',
            'email'            => 'nullable|email|max:150',
            'website'          => 'nullable|url|max:255',
            'checkin_time'     => 'required',
            'checkout_time'    => 'required',
            'currency'         => 'required|string|max:10',
            'timezone'         => 'required|string|max:60',
            'vat_rate'         => 'required|numeric|min:0|max:100',
            'total_rooms'      => 'required|integer|min:0',
            'facebook'         => 'nullable|url|max:255',
            'instagram'        => 'nullable|url|max:255',
            'tripadvisor'      => 'nullable|url|max:255',
            'booking_com'      => 'nullable|url|max:255',
        ]);

        CompanySetting::instance()->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Company settings saved.']);
        }
        return back()->with('success', 'Company settings saved.');
    }

    public function uploadLogo(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate(['logo' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048']);

        $setting = CompanySetting::instance();

        if ($setting->logo) {
            Storage::disk('public')->delete($setting->logo);
        }

        $ext  = $request->file('logo')->getClientOriginalExtension();
        $path = $request->file('logo')->storeAs('logos', Str::uuid() . '.' . $ext, 'public');
        $setting->update(['logo' => $path]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Logo updated.']);
        }
        return back()->with('success', 'Logo updated.');
    }
}
