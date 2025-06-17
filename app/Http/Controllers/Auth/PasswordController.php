<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        $user = Auth::user();
        if($user->role == 'salesRep')
        {
            $credentials = [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => $validated['password'],
        ];

        $csvPath = 'exports/sales_reps_new_passwords_.csv';
        if (!Storage::exists($csvPath)) {
            Storage::put($csvPath, "Name,Username,Email,Password\n");
        }
        Storage::append($csvPath, implode(',', $credentials) . "\n");

        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
