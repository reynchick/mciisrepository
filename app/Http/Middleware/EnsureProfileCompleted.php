<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     * 
     * Redirect users with incomplete profiles to the appropriate completion page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip if no authenticated user
        if (!$user) {
            return $next($request);
        }

        // Skip if profile is already completed
        if ($user->profile_completed) {
            return $next($request);
        }

        // Skip for profile completion routes themselves
        if ($request->routeIs('student.profile.complete') || 
            $request->routeIs('faculty.profile.complete') ||
            $request->routeIs('student.profile.complete.store') ||
            $request->routeIs('faculty.profile.complete.store') ||
            $request->routeIs('logout')) {
            return $next($request);
        }

        // Redirect students without student_id to complete profile
        if ($user->isStudent() && !$user->student_id) {
            return redirect()->route('student.profile.complete')
                ->with('status', 'Please complete your profile to continue.');
        }

        // Redirect faculty with profile_completed = false to verify/update their profile on first login
        // Note: Faculty data is pre-seeded, so this is just to let them verify/update on first use
        if ($user->isFaculty() && $user->faculty_id) {
            return redirect()->route('faculty.profile.complete')
                ->with('status', 'Welcome! Please verify and update your profile information.');
        }

        return $next($request);
    }
}
