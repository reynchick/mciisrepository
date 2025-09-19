<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip if user is not authenticated
        if (!$user) {
            return $next($request);
        }

        // Skip if user doesn't need password change
        if (!$user->needsPasswordChange()) {
            return $next($request);
        }

        // Allow access to password change routes
        if ($request->routeIs(['auth.change-password.*', 'logout'])) {
            return $next($request);
        }

        // Redirect to password change page
        return redirect()->route('auth.change-password.show')
            ->with('status', 'Please change your temporary password to something more secure.');
    }
}
