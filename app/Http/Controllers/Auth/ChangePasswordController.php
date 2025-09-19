<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ChangePasswordController extends Controller
{
    /**
     * Show the password change form.
     */
    public function show(): Response
    {
        $user = auth()->user();
        
        return Inertia::render('auth/change-password', [
            'user' => [
                'name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role->name,
            ]
        ]);
    }

    /**
     * Handle password change request.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validate the request
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Please enter your current password.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.'
            ]);
        }

        // Check if new password is different from current
        if (Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'The new password must be different from your current password.'
            ]);
        }

        // Update password and mark as changed
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);
        
        $user->markPasswordChanged();

        return redirect()->route('dashboard')
            ->with('success', 'Your password has been changed successfully. Welcome to the system!');
    }
}
