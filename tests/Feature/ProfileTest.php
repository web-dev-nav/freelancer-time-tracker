<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role = 'author'): User
    {
        return User::create([
            'name' => 'Profile User', 'email' => "profile-{$role}@example.test",
            'password' => 'Original-password-123', 'role' => $role,
        ]);
    }

    public function test_profile_requires_authentication_and_is_available_to_both_roles(): void
    {
        $this->get('/profile')->assertRedirect('/login');
        foreach (['author', 'client'] as $role) {
            $user = $this->account($role);
            $this->actingAs($user)->get('/profile')->assertOk()->assertSee('Email address')->assertSee('Save changes');
            $this->get('/')->assertOk()->assertSee('aria-label="Profile"', false);
        }
    }

    public function test_email_can_change_on_its_own_without_changing_password_access_or_billing_contacts(): void
    {
        $user = $this->account('client');
        $oldEmail = $user->email;
        $oldHash = $user->password;
        $organization = Project::create(['name' => 'Client organization', 'client_user_id' => $user->id, 'client_email' => $oldEmail, 'status' => 'active']);
        $this->actingAs($user)->patch('/profile', [
            'email' => ' New-Login@Example.Test ', 'current_password' => 'Original-password-123',
            'password' => '', 'password_confirmation' => '',
            'role' => 'author', 'name' => 'Attempted replacement', 'id' => 1,
        ])->assertRedirect('/profile')->assertSessionHas('status', 'profile-updated');
        $user->refresh();
        $this->assertSame('new-login@example.test', $user->email);
        $this->assertSame($oldHash, $user->password);
        $this->assertSame('client', $user->role);
        $this->assertSame('Profile User', $user->name);
        $this->assertSame($user->id, $organization->fresh()->client_user_id);
        $this->assertSame($oldEmail, $organization->fresh()->client_email);
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_current_password_or_duplicate_email_cannot_change_any_account_field(): void
    {
        $user = $this->account();
        $other = $this->account('client');
        $this->actingAs($user)->from('/profile')->patch('/profile', [
            'email' => 'unused@example.test', 'current_password' => 'Wrong-password',
            'password' => 'Replacement-password-456', 'password_confirmation' => 'Replacement-password-456',
        ])->assertRedirect('/profile')->assertSessionHasErrors('current_password')->assertSessionMissing('_old_input.current_password')->assertSessionMissing('_old_input.password');
        $this->patch('/profile', [
            'email' => $other->email, 'current_password' => 'Original-password-123',
            'password' => 'Replacement-password-456', 'password_confirmation' => 'Replacement-password-456',
        ])->assertSessionHasErrors('email');
        $this->assertSame('profile-author@example.test', $user->fresh()->email);
        $this->assertTrue(Hash::check('Original-password-123', $user->fresh()->password));
    }

    public function test_email_and_password_can_change_together_and_the_new_credentials_work(): void
    {
        $user = $this->account();
        $user->setRememberToken('previous-remember-token');
        $user->save();
        $this->actingAs($user)->patch('/profile', [
            'email' => 'new-login@example.test', 'current_password' => 'Original-password-123',
            'password' => 'Replacement-password-456', 'password_confirmation' => 'Replacement-password-456',
        ])->assertRedirect('/profile')->assertSessionHas('status', 'profile-updated');
        $user->refresh();
        $this->assertSame('new-login@example.test', $user->email);
        $this->assertTrue(Hash::check('Replacement-password-456', $user->password));
        $this->assertFalse(Hash::check('Original-password-123', $user->password));
        $this->assertNotSame('previous-remember-token', $user->getRememberToken());
        $this->assertAuthenticatedAs($user);
        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'Original-password-123'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $user->email, 'password' => 'Replacement-password-456'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_can_change_without_changing_the_email(): void
    {
        $user = $this->account();
        $this->actingAs($user)->patch('/profile', [
            'email' => $user->email, 'current_password' => 'Original-password-123',
            'password' => 'Replacement-password-456', 'password_confirmation' => 'Replacement-password-456',
        ])->assertRedirect('/profile')->assertSessionHas('status', 'profile-updated');
        $this->assertSame('profile-author@example.test', $user->fresh()->email);
        $this->assertTrue(Hash::check('Replacement-password-456', $user->fresh()->password));
    }

    public function test_password_validation_does_not_partially_save_email_or_flash_secrets(): void
    {
        $user = $this->account();
        $this->actingAs($user)->from('/profile')->patch('/profile', [
            'email' => 'unsaved@example.test', 'current_password' => 'Original-password-123',
            'password' => 'short', 'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
        $this->patch('/profile', [
            'email' => 'unsaved@example.test', 'current_password' => 'Original-password-123',
            'password' => 'Replacement-password-456', 'password_confirmation' => 'Mismatch-password-789',
        ])->assertSessionHasErrors('password')->assertSessionMissing('_old_input.password')->assertSessionMissing('_old_input.password_confirmation');
        $this->assertSame('profile-author@example.test', $user->fresh()->email);
        $this->assertTrue(Hash::check('Original-password-123', $user->fresh()->password));
    }

    public function test_confirmation_without_a_new_password_is_rejected(): void
    {
        $user = $this->account();
        $this->actingAs($user)->patch('/profile', [
            'email' => 'unsaved@example.test', 'current_password' => 'Original-password-123',
            'password_confirmation' => 'Replacement-password-456',
        ])->assertSessionHasErrors('password');
        $this->assertSame('profile-author@example.test', $user->fresh()->email);
    }

    public function test_profile_updates_require_csrf_even_though_other_legacy_routes_disable_it(): void
    {
        $user = $this->account();
        $this->app['env'] = 'production';
        $this->actingAs($user)->withSession(['_token' => 'profile-csrf-token'])->patch('/profile', [
            'email' => 'changed@example.test', 'current_password' => 'Original-password-123',
        ])->assertStatus(419);
        $this->assertSame('profile-author@example.test', $user->fresh()->email);
        $this->patch('/profile', [
            '_token' => 'profile-csrf-token', 'email' => 'changed@example.test', 'current_password' => 'Original-password-123',
        ])->assertRedirect('/profile');
    }
}
