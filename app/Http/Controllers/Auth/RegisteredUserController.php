<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'student_id' => 'nullable|regex:/^\d{4}-\d{5}$/|unique:users,student_id',
            'contact_number' => ['required', 'regex:/^(09|\+63\s?9)\d{9}$/'],
            'email' => 'required|string|lowercase|email|max:255|unique:users,email|regex:/^[^@]+@usep\.edu\.ph$/',
            'role' => 'required|in:Faculty,Student',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'student_id.regex' => 'Student ID must be in format YYYY-NNNNN (e.g., 2023-00800)',
            'contact_number.regex' => 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +63 9XXXXXXXXX)',
            'email.regex' => 'Email must be a valid USeP email address ending with @usep.edu.ph',
            'role.in' => 'Only Faculty and Student roles are allowed for registration.',
        ]);

        // Require student_id if role = Student
        if ($request->role === 'Student' && !$request->student_id) {
            throw ValidationException::withMessages([
                'student_id' => 'Student ID is required for student accounts.'
            ]);
        }

        // Validate Faculty role against Faculty table
        if ($request->role === 'Faculty') {
            $faculty = Faculty::where('email', $request->email)->first();
            if (!$faculty) {
                throw ValidationException::withMessages([
                    'email' => 'This email is not registered in our faculty database. Please contact the administrator.'
                ]);
            }
        }

        // Resolve role_id from Role model
        $role = Role::where('name', $request->role)->first();
        if (!$role) {
            throw ValidationException::withMessages([
                'role' => 'Selected role is invalid.',
            ]);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'student_id' => $request->student_id,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'role_id' => $role->id, // ✅ save role_id instead of role
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return redirect()->route('verification.notice')
            ->with('status', 'Account created successfully! Please check your email to verify your account before logging in.');
    }
}
