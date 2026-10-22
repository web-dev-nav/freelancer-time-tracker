@extends('layouts.app')

@section('title', 'Profile · Timesheet')
@section('main-anchor', 'profile-title')

@section('content')
<div class="profile-page">
    <a class="profile-back" href="{{ route('timesheet.index') }}"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back to workspace</a>
    <div class="profile-heading">
        <p class="eyebrow">YOUR ACCOUNT</p>
        <h1 id="profile-title" tabindex="-1">Profile</h1>
        <p>Update your sign-in details in one place.</p>
    </div>

    @if(session('status') === 'profile-updated')
        <div class="alert alert-success" role="status">Your account changes have been saved.</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="profile-form" aria-label="Account details">
        @csrf
        @method('PATCH')
        <div class="profile-field">
            <label class="form-label" for="profile-email">Email address</label>
            <input type="email" id="profile-email" name="email" autocomplete="username" value="{{ old('email', $user->email) }}" required maxlength="255" class="form-control @error('email') is-invalid @enderror" @error('email') aria-invalid="true" aria-describedby="profile-email-error" @enderror>
            @error('email')<p class="invalid-feedback" id="profile-email-error">{{ $message }}</p>@enderror
        </div>
        <div class="profile-field">
            <label class="form-label" for="profile-current-password">Current password</label>
            <input type="password" id="profile-current-password" name="current_password" autocomplete="current-password" required class="form-control @error('current_password') is-invalid @enderror" aria-describedby="profile-current-password-help @error('current_password') profile-current-password-error @enderror" @error('current_password') aria-invalid="true" @enderror>
            <p class="profile-field-help" id="profile-current-password-help">Enter your current password to confirm your changes.</p>
            @error('current_password')<p class="invalid-feedback" id="profile-current-password-error">{{ $message }}</p>@enderror
        </div>

        <details class="profile-password-options" @if($errors->has('password') || $errors->has('password_confirmation')) open @endif>
            <summary>Change password <span class="text-muted">(optional)</span></summary>
            <p class="profile-field-help" id="profile-password-help">Leave these fields blank to keep your password. A new password must have at least 8 characters.</p>
            <div class="profile-field">
                <label class="form-label" for="profile-password">New password</label>
                <input type="password" id="profile-password" name="password" autocomplete="new-password" minlength="8" class="form-control @error('password') is-invalid @enderror" aria-describedby="profile-password-help @error('password') profile-password-error @enderror" @error('password') aria-invalid="true" @enderror>
                @error('password')<p class="invalid-feedback" id="profile-password-error">{{ $message }}</p>@enderror
            </div>
            <div class="profile-field">
                <label class="form-label" for="profile-password-confirmation">Confirm new password</label>
                <input type="password" id="profile-password-confirmation" name="password_confirmation" autocomplete="new-password" minlength="8" class="form-control @error('password_confirmation') is-invalid @enderror" @error('password_confirmation') aria-invalid="true" aria-describedby="profile-password-confirmation-error" @enderror>
                @error('password_confirmation')<p class="invalid-feedback" id="profile-password-confirmation-error">{{ $message }}</p>@enderror
            </div>
        </details>

        <div class="profile-form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a href="{{ route('timesheet.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
