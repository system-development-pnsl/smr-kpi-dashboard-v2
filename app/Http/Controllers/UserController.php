<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('department')->orderBy('full_name')->paginate(25);
        return view('pages.users.index', ['users' => $users, 'departments' => Department::orderBy('sort_order')->get()]);
    }

    public function create(): View
    {
        return view('pages.users.form', ['departments' => Department::orderBy('sort_order')->get()]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'full_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'role'          => 'required|in:owner,general_manager,agm,finance_director,head_of_dept,staff',
            'department_id' => 'required|exists:departments,id',
            'job_title'     => 'required|string|max:100',
        ]);
        $dept  = Department::find($data['department_id']);
        $count = User::where('department_id', $data['department_id'])->count() + 1;
        User::create([...$data, 'code' => $dept->code.'-'.str_pad($count,3,'0',STR_PAD_LEFT), 'password' => Hash::make('Password@2026'), 'status' => 'active', 'access_modules' => ['tasks','kpi']]);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User created.', 'redirect' => route('users.index')]);
        }
        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        return view('pages.users.form', ['user' => $user, 'departments' => Department::orderBy('sort_order')->get()]);
    }

    public function update(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $user->update($request->validate(['full_name' => 'required|string|max:100', 'job_title' => 'required|string|max:100', 'role' => 'required', 'department_id' => 'required|exists:departments,id']));
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User updated.', 'redirect' => route('users.index')]);
        }
        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): JsonResponse|RedirectResponse
    {
        $user->update(['status' => 'inactive']);
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User deactivated.', 'redirect' => route('users.index')]);
        }
        return redirect()->route('users.index')->with('success', 'User deactivated.');
    }

    public function profile(): View
    {
        return view('pages.users.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request): JsonResponse|RedirectResponse
    {
        auth()->user()->update($request->validate(['full_name' => 'required|string|max:100', 'phone' => 'nullable|string|max:20']));
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Profile updated.']);
        }
        return back()->with('success', 'Profile updated.');
    }

    public function updatePhoto(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048']);

        $user = auth()->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $ext  = $request->file('photo')->getClientOriginalExtension();
        $path = $request->file('photo')->storeAs('profile-photos', Str::uuid() . '.' . $ext, 'public');

        $user->update(['profile_photo' => $path]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Profile photo updated.']);
        }
        return back()->with('success', 'Profile photo updated.');
    }

    public function changePassword(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate(['current_password' => 'required', 'password' => 'required|min:10|confirmed']);
        if (! Hash::check($request->current_password, auth()->user()->password)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Current password is incorrect.', 'errors' => ['current_password' => ['Current password is incorrect.']]], 422);
            }
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        auth()->user()->update(['password' => Hash::make($request->password)]);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Password changed.']);
        }
        return back()->with('success', 'Password changed.');
    }
}
