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

    return view('profile.show', compact('user', 'salesRep', 'translatedPermissions'));
}

public function updatePhoto(Request $request)
{
    $request->validate([
        'profile_photo_path' => 'required|image|max:2048',
    ]);

    $user = Auth::user();

    if ($user->personal_image && Storage::exists('public/' . $user->personal_image)) {
        Storage::delete('public/' . $user->personal_image);
    }

    $path = $request->file('profile_photo_path')->store('profile_photos', 'public');

    $user->personal_image = $path;
    $user->save();

    return redirect()->back()->with('success', 'تم تحديث الصورة الشخصية بنجاح.');
}

}
