<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'current_password:web'],
            'password' => ['nullable', 'required_with:password_confirmation', 'string', Password::min(8), 'confirmed', 'different:current_password'],
            'password_confirmation' => ['nullable', 'required_with:password', 'string'],
        ], [
            'current_password.current_password' => 'Your current password is incorrect.',
            'password.confirmed' => 'The new passwords do not match.',
            'password.required_with' => 'Enter a new password or clear both new-password fields.',
            'password_confirmation.required_with' => 'Confirm your new password.',
        ]);

        $emailChanged = $user->email !== $validated['email'];
        $passwordChanged = filled($validated['password'] ?? null);
        if ($emailChanged) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
        }
        if ($passwordChanged) {
            // The User password cast hashes the new value before storing it.
            $user->password = $validated['password'];
        }
        if ($emailChanged || $passwordChanged) {
            $user->setRememberToken(Str::random(60));
            $user->save();
            $request->session()->regenerate();
        }

        return to_route('profile.edit')->with('status', 'profile-updated');
    }
}
