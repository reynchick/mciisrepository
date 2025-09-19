<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('redirects admin users to password change page on first login', function () {
    $adminRole = Role::factory()->create(['name' => 'Administrator']);
    $user = User::factory()->create([
        'role_id' => $adminRole->id,
        'password' => Hash::make('TempPassword123!'),
        'must_change_password' => true,
        'is_temporary_password' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'TempPassword123!',
    ]);

    $response->assertRedirect('/change-password');
    $response->assertSessionHas('status', 'Please change your temporary password to something more secure.');
});

it('redirects staff users to password change page on first login', function () {
    $staffRole = Role::factory()->create(['name' => 'MCIIS Staff']);
    $user = User::factory()->create([
        'role_id' => $staffRole->id,
        'password' => Hash::make('TempPassword123!'),
        'must_change_password' => true,
        'is_temporary_password' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'TempPassword123!',
    ]);

    $response->assertRedirect('/change-password');
});

it('does not redirect regular users to password change page', function () {
    $studentRole = Role::factory()->create(['name' => 'Student']);
    $user = User::factory()->create([
        'role_id' => $studentRole->id,
        'email_verified_at' => now(),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
});

it('shows password change form to admin users', function () {
    $adminRole = Role::factory()->create(['name' => 'Administrator']);
    $user = User::factory()->create([
        'role_id' => $adminRole->id,
        'must_change_password' => true,
        'is_temporary_password' => true,
    ]);

    $response = $this->actingAs($user)->get('/change-password');

    $response->assertOk();
    $response->assertInertia(fn ($page) => 
        $page->component('auth/change-password')
            ->has('user')
    );
});

it('successfully changes password for admin users', function () {
    $adminRole = Role::factory()->create(['name' => 'Administrator']);
    $user = User::factory()->create([
        'role_id' => $adminRole->id,
        'password' => Hash::make('TempPassword123!'),
        'must_change_password' => true,
        'is_temporary_password' => true,
    ]);

    $response = $this->actingAs($user)->post('/change-password', [
        'current_password' => 'TempPassword123!',
        'password' => 'NewSecurePassword123!',
        'password_confirmation' => 'NewSecurePassword123!',
    ]);

    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('success', 'Your password has been changed successfully. Welcome to the system!');

    $user->refresh();
    expect($user->must_change_password)->toBeFalse();
    expect($user->is_temporary_password)->toBeFalse();
    expect($user->password_changed_at)->not->toBeNull();
    expect(Hash::check('NewSecurePassword123!', $user->password))->toBeTrue();
});

it('validates current password correctly', function () {
    $adminRole = Role::factory()->create(['name' => 'Administrator']);
    $user = User::factory()->create([
        'role_id' => $adminRole->id,
        'password' => Hash::make('TempPassword123!'),
        'must_change_password' => true,
    ]);

    $response = $this->actingAs($user)->post('/change-password', [
        'current_password' => 'WrongPassword',
        'password' => 'NewSecurePassword123!',
        'password_confirmation' => 'NewSecurePassword123!',
    ]);

    $response->assertSessionHasErrors(['current_password']);
});

it('prevents using same password as new password', function () {
    $adminRole = Role::factory()->create(['name' => 'Administrator']);
    $user = User::factory()->create([
        'role_id' => $adminRole->id,
        'password' => Hash::make('TempPassword123!'),
        'must_change_password' => true,
    ]);

    $response = $this->actingAs($user)->post('/change-password', [
        'current_password' => 'TempPassword123!',
        'password' => 'TempPassword123!',
        'password_confirmation' => 'TempPassword123!',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('blocks access to dashboard until password is changed', function () {
    $adminRole = Role::factory()->create(['name' => 'Administrator']);
    $user = User::factory()->create([
        'role_id' => $adminRole->id,
        'must_change_password' => true,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertRedirect('/change-password');
    $response->assertSessionHas('status', 'Please change your temporary password to something more secure.');
});

it('allows access to dashboard after password change', function () {
    $adminRole = Role::factory()->create(['name' => 'Administrator']);
    $user = User::factory()->create([
        'role_id' => $adminRole->id,
        'must_change_password' => false,
        'is_temporary_password' => false,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
});
