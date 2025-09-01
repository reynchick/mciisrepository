<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rule;

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
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'studentID' => 'nullable|regex:/^\d{4}-\d{5}$/|unique:users,studentID',
            'contactNumber' => ['required', 'regex:/^(09|\+63\s?9)\d{9}$/'],
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class.'|regex:/^[^@]+@usep\.edu\.ph$/',
            'role' => 'required|in:Administrator,MCIIS Staff,Faculty,Student',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'studentID.regex' => 'Student ID must be in format YYYY-NNNNN (e.g., 2023-00800)',
            'contactNumber.regex' => 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +63 9XXXXXXXXX)',
            'email.regex' => 'Email must be a valid USeP email address ending with @usep.edu.ph',
        ]);

        // Make studentID required for Student role
        if ($request->role === 'Student' && !$request->studentID) {
            return back()->withErrors(['studentID' => 'Student ID is required for student accounts.']);
        }

        $user = User::create([
            'firstName' => $request->firstName,
            'middleName' => $request->middleName,
            'lastName' => $request->lastName,
            'studentID' => $request->studentID,
            'contactNumber' => $request->contactNumber,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
