<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

public function show()
{
    $user = Auth::user();
    $user->load('permissions');

    $translatedPermissions = $user->permissions->map(function ($permission) {
        return __($permission->name);
    });

    // Check if user is a salesRep and load related model
    $salesRep = null;
    if ($user->role === 'salesRep') {
        $salesRep = $user->salesRep; // assuming hasOne relationship

    }
//dd($user->personal_image);
    return view('profile.show', compact('user', 'salesRep', 'translatedPermissions'));
}

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo_path' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        // Delete old photo if exists (local only)
        if ($user->personal_image && Storage::disk('public')->exists($user->personal_image)) {
            Storage::disk('public')->delete($user->personal_image);
        }

        // Store in local storage (public disk)
        $file = $request->file('profile_photo_path');
        $path = $file->store('profile-photos', 'public'); // stored in storage/app/public/profile-photos

        // Update user record
        $user->personal_image = $path;
        $user->save();

        // Debug info to confirm path and existence
        dd([
            'path' => $path,
            'exists_in_local' => Storage::disk('public')->exists($path),
            'public_url' => Storage::url($path),
        ]);
    }

}
