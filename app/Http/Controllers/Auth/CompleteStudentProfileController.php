<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Observers\UserObserver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompleteStudentProfileController extends Controller
{
    /**
     * Show the profile completion page for new students.
     * 
     * Students who register via Google SSO need to provide:
     * - Student ID (cannot be extracted from email)
     * - Contact number (optional)
     */
    public function show(Request $request): Response|RedirectResponse
    {
        // Only students without student_id need to complete profile
        if (!$request->user()->isStudent() || $request->user()->student_id) {
            return redirect()->route('dashboard');
        }
        
        return Inertia::render('auth/complete-student-profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Store the completed profile information.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'regex:/^\d{4}-\d{5}$/', 'unique:users,student_id'],
            'contact_number' => ['nullable', 'regex:/^(09|\+63\s?9)\d{9}$/'],
        ], [
            'student_id.regex' => 'Student ID must be in format YYYY-NNNNN (e.g., 2023-00800)',
            'contact_number.regex' => 'Please enter a valid Philippine mobile number',
        ]);

        // Set custom metadata for UserObserver before updating
        UserObserver::$customMetadata = [
            'action' => 'profile_completion',
            'note' => 'Student completed profile after first login',
        ];
        
        // Update user - UserObserver will automatically log this
        $user->update($validated);
        
        return redirect()->route('dashboard')
            ->with('status', 'Profile completed successfully!');
    }
}