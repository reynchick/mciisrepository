<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Observers\FacultyObserver;
use App\Observers\UserObserver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompleteFacultyProfileController extends Controller
{
    /**
     * Show the faculty profile completion page.
     */
    public function show(Request $request): Response|RedirectResponse
    {
        // Only faculty with incomplete profiles need this
        if (!$request->user()->isFaculty() || !$request->user()->faculty_id) {
            return redirect()->route('dashboard');
        }

        $faculty = Faculty::find($request->user()->faculty_id);

        if (!$faculty) {
            return redirect()->route('dashboard');
        }

        // Check if profile is already complete
        if ($faculty->position && $faculty->designation && $faculty->contact_number) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('auth/complete-faculty-profile', [
            'user' => $request->user(),
            'faculty' => $faculty,
        ]);
    }

    /**
     * Store the completed faculty profile information.
     * 
     * Faculty can only edit their own profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->isFaculty() || !$user->faculty_id) {
            abort(403, 'Unauthorized');
        }

        $faculty = Faculty::find($user->faculty_id);

        if (!$faculty) {
            abort(404, 'Faculty record not found');
        }

        // Validate input (faculty_id, email, and orcid are read-only)
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'regex:/^(09|\+63\s?9)\d{9}$/'],
            'educational_attainment' => ['nullable', 'string', 'max:255'],
            'field_of_specialization' => ['nullable', 'string'],
            'research_interest' => ['nullable', 'string'],
        ], [
            'contact_number.regex' => 'Please enter a valid Philippine mobile number',
        ]);

        // Set custom metadata for UserObserver before updating user
        UserObserver::$customMetadata = [
            'action' => 'profile_completion',
            'note' => 'Faculty completed profile after first login',
        ];

        // Update user record - UserObserver will automatically log this
        $user->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'contact_number' => $validated['contact_number'],
        ]);

        // Set custom metadata for FacultyObserver before updating faculty
        FacultyObserver::$customMetadata = [
            'action' => 'profile_completion',
            'note' => 'Faculty completed profile after first login',
        ];

        // Update faculty record - FacultyObserver will automatically log this
        // Only update editable fields (not faculty_id, email, or orcid)
        $faculty->update([
            'position' => $validated['position'],
            'designation' => $validated['designation'],
            'contact_number' => $validated['contact_number'],
            'educational_attainment' => $validated['educational_attainment'],
            'field_of_specialization' => $validated['field_of_specialization'],
            'research_interest' => $validated['research_interest'],
        ]);

        return redirect()->route('dashboard')
            ->with('status', 'Faculty profile completed successfully!');
    }
}