<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile settings.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show the profile completion page for new students.
     */
    public function showComplete(Request $request): Response|RedirectResponse
    {
        // Only students without student_id need to complete profile
        if (!$request->user()->isStudent() || $request->user()->student_id) {
            return redirect()->route('dashboard');
        }
        
        return Inertia::render('profile/complete', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Store the completed profile information.
     */
    public function storeComplete(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|regex:/^\d{4}-\d{5}$/|unique:users,student_id',
            'contact_number' => 'required|regex:/^(09|\+63\s?9)\d{9}$/',
        ], [
            'student_id.regex' => 'Student ID must be in format YYYY-NNNNN (e.g., 2023-00800)',
            'contact_number.regex' => 'Please enter a valid Philippine mobile number',
        ]);
        
        $request->user()->update([
            'student_id' => $request->student_id,
            'contact_number' => $request->contact_number,
        ]);
        
        return redirect()->route('dashboard')
            ->with('status', 'Profile completed successfully!');
    }
}
