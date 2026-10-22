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

    public function updateEmail(Request $request)
    {
        $user = $request->user();
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $validated = $request->validateWithBag('emailUpdate', [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'current_password:web'],
        ]);

        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
            $user->setRememberToken(Str::random(60));
            $user->save();
            $request->session()->regenerate();
        }

        return to_route('profile.edit')->with('status', 'email-updated');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'string', Password::min(8), 'confirmed', 'different:current_password'],
        ]);

        $user = $request->user();
        // The User password cast hashes the new value before storing it.
        $user->password = $validated['password'];
        $user->setRememberToken(Str::random(60));
        $user->save();
        $request->session()->regenerate();

        return to_route('profile.edit')->with('status', 'password-updated');
    }
}
