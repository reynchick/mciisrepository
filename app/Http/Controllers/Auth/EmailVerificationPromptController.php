<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Show the email verification prompt page.
     */
    public function __invoke(Request $request): Response|RedirectResponse
    {
        // If user is authenticated and verified, redirect to dashboard
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // If user is authenticated but not verified, show verification page
        if ($request->user()) {
            return Inertia::render('auth/verify-email', [
                'status' => $request->session()->get('status')
            ]);
        }

        // If user is not authenticated, show verification notice for guests
        return Inertia::render('auth/verify-email', [
            'status' => $request->session()->get('status'),
            'isGuest' => true
        ]);
    }
}


