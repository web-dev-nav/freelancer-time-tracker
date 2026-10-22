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
            'email_verified_at' => now(),
        ]);
    }

    public function test_profile_requires_authentication_and_is_available_to_both_roles(): void
    {
        $this->get('/profile')->assertRedirect('/login');
        foreach (['author', 'client'] as $role) {
            $user = $this->account($role);
            $this->actingAs($user)->get('/profile')->assertOk()->assertSee('Login email')->assertSee('Confirm new password');
            $this->get('/')->assertOk()->assertSee('aria-label="Profile"', false);
        }
    }

    public function test_email_change_keeps_organization_access_and_does_not_change_billing_contacts_or_role(): void
    {
        $user = $this->account('client');
        $oldEmail = $user->email;
        $organization = Project::create(['name' => 'Client organization', 'client_user_id' => $user->id, 'client_email' => $oldEmail, 'status' => 'active']);
        $this->actingAs($user)->patch('/profile/email', [
            'email' => ' New-Login@Example.Test ', 'current_password' => 'Original-password-123',
            'role' => 'author', 'name' => 'Attempted replacement', 'id' => 1,
        ])->assertRedirect('/profile')->assertSessionHas('status', 'email-updated');
        $user->refresh();
        $this->assertSame('new-login@example.test', $user->email);
        $this->assertSame('client', $user->role);
        $this->assertSame('Profile User', $user->name);
        $this->assertSame($user->id, $organization->fresh()->client_user_id);
        $this->assertSame($oldEmail, $organization->fresh()->client_email);
        $this->assertAuthenticatedAs($user);
        $this->post('/logout');
        $this->post('/login', ['email' => 'new-login@example.test', 'password' => 'Original-password-123'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_email_change_rejects_wrong_password_and_duplicate_email_without_flashing_passwords(): void
    {
        $user = $this->account();
        $other = $this->account('client');
        $this->actingAs($user)->from('/profile')->patch('/profile/email', [
            'email' => 'unused@example.test', 'current_password' => 'Wrong-password',
        ])->assertRedirect('/profile')->assertSessionHasErrorsIn('emailUpdate', 'current_password')->assertSessionMissing('_old_input.current_password');
        $this->patch('/profile/email', [
            'email' => $other->email, 'current_password' => 'Original-password-123',
        ])->assertSessionHasErrorsIn('emailUpdate', 'email');
        $this->assertSame('profile-author@example.test', $user->fresh()->email);
    }

    public function test_password_update_hashes_the_new_password_keeps_the_session_and_changes_login_credentials(): void
    {
        $user = $this->account();
        $user->setRememberToken('previous-remember-token');
        $user->save();
        $this->actingAs($user)->patch('/profile/password', [
            'current_password' => 'Original-password-123', 'password' => 'Replacement-password-456', 'password_confirmation' => 'Replacement-password-456',
        ])->assertRedirect('/profile')->assertSessionHas('status', 'password-updated');
        $user->refresh();
        $this->assertTrue(Hash::check('Replacement-password-456', $user->password));
        $this->assertFalse(Hash::check('Original-password-123', $user->password));
        $this->assertNotSame('previous-remember-token', $user->getRememberToken());
        $this->assertAuthenticatedAs($user);
        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'Original-password-123'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $user->email, 'password' => 'Replacement-password-456'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_update_requires_the_current_password_minimum_length_and_matching_confirmation(): void
    {
        $user = $this->account();
        $this->actingAs($user)->patch('/profile/password', [
            'current_password' => 'Wrong-password', 'password' => 'Replacement-password-456', 'password_confirmation' => 'Replacement-password-456',
        ])->assertSessionHasErrorsIn('passwordUpdate', 'current_password');
        $this->patch('/profile/password', [
            'current_password' => 'Original-password-123', 'password' => 'short', 'password_confirmation' => 'short',
        ])->assertSessionHasErrorsIn('passwordUpdate', 'password');
        $this->patch('/profile/password', [
            'current_password' => 'Original-password-123', 'password' => 'Replacement-password-456', 'password_confirmation' => 'Mismatch-password-789',
        ])->assertSessionHasErrorsIn('passwordUpdate', 'password')->assertSessionMissing('_old_input.password')->assertSessionMissing('_old_input.password_confirmation');
        $this->assertTrue(Hash::check('Original-password-123', $user->fresh()->password));
    }

    public function test_profile_updates_require_csrf_even_though_other_legacy_routes_disable_it(): void
    {
        $user = $this->account();
        // CSRF normally bypasses unit-test requests; use the production environment for this check.
        $this->app['env'] = 'production';
        $this->actingAs($user)->withSession(['_token' => 'profile-csrf-token'])->patch('/profile/email', [
            'email' => 'changed@example.test', 'current_password' => 'Original-password-123',
        ])->assertStatus(419);
        $this->assertSame('profile-author@example.test', $user->fresh()->email);
        $this->patch('/profile/email', [
            '_token' => 'profile-csrf-token', 'email' => 'changed@example.test', 'current_password' => 'Original-password-123',
        ])->assertRedirect('/profile');
    }
}
