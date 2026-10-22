@extends('layouts.app')

@section('title', 'Profile · Timesheet')
@section('main-anchor', 'profile-title')

@section('content')
<div class="profile-page">
    <a class="profile-back" href="{{ route('timesheet.index') }}"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back to workspace</a>
    <div class="profile-heading">
        <p class="eyebrow">YOUR ACCOUNT</p>
        <h1 id="profile-title" tabindex="-1">Profile</h1>
        <p>Manage the email and password you use to sign in.</p>
    </div>

    <div class="profile-layout">
        <section class="profile-card" aria-labelledby="profile-email-title">
            <div class="profile-card-icon"><i class="fas fa-envelope" aria-hidden="true"></i></div>
            <h2 id="profile-email-title">Login email</h2>
            <p class="profile-description">Use this email the next time you sign in. Your organization access and billing contacts stay the same.</p>
            @if(session('status') === 'email-updated')
                <div class="alert alert-success" role="status">Your login email has been saved.</div>
            @endif
            <form method="POST" action="{{ route('profile.email.update') }}">
                @csrf
                @method('PATCH')
                <div class="profile-field">
                    <label class="form-label" for="profile-email">Email address</label>
                    <input type="email" id="profile-email" name="email" autocomplete="username" value="{{ old('email', $user->email) }}" required maxlength="255" class="form-control @error('email', 'emailUpdate') is-invalid @enderror" @error('email', 'emailUpdate') aria-invalid="true" aria-describedby="profile-email-error" @enderror>
                    @error('email', 'emailUpdate')<p class="invalid-feedback" id="profile-email-error">{{ $message }}</p>@enderror
                </div>
                <div class="profile-field">
                    <label class="form-label" for="email-current-password">Current password</label>
                    <input type="password" id="email-current-password" name="current_password" autocomplete="current-password" required class="form-control @error('current_password', 'emailUpdate') is-invalid @enderror" @error('current_password', 'emailUpdate') aria-invalid="true" aria-describedby="email-current-password-error" @enderror>
                    @error('current_password', 'emailUpdate')<p class="invalid-feedback" id="email-current-password-error">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Save email</button>
            </form>
        </section>

        <section class="profile-card" aria-labelledby="profile-password-title">
            <div class="profile-card-icon"><i class="fas fa-lock" aria-hidden="true"></i></div>
            <h2 id="profile-password-title">Password</h2>
            <p class="profile-description">Choose a new password with at least 8 characters. You’ll stay signed in on this device.</p>
            @if(session('status') === 'password-updated')
                <div class="alert alert-success" role="status">Your password has been updated.</div>
            @endif
            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="username" value="{{ $user->email }}" autocomplete="username">
                <div class="profile-field">
                    <label class="form-label" for="password-current-password">Current password</label>
                    <input type="password" id="password-current-password" name="current_password" autocomplete="current-password" required class="form-control @error('current_password', 'passwordUpdate') is-invalid @enderror" @error('current_password', 'passwordUpdate') aria-invalid="true" aria-describedby="password-current-error" @enderror>
                    @error('current_password', 'passwordUpdate')<p class="invalid-feedback" id="password-current-error">{{ $message }}</p>@enderror
                </div>
                <div class="profile-field">
                    <label class="form-label" for="profile-password">New password</label>
                    <input type="password" id="profile-password" name="password" autocomplete="new-password" required minlength="8" class="form-control @error('password', 'passwordUpdate') is-invalid @enderror" @error('password', 'passwordUpdate') aria-invalid="true" aria-describedby="profile-password-error" @enderror>
                    @error('password', 'passwordUpdate')<p class="invalid-feedback" id="profile-password-error">{{ $message }}</p>@enderror
                </div>
                <div class="profile-field">
                    <label class="form-label" for="profile-password-confirmation">Confirm new password</label>
                    <input type="password" id="profile-password-confirmation" name="password_confirmation" autocomplete="new-password" required minlength="8" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Update password</button>
            </form>
        </section>
    </div>
</div>
@endsection
