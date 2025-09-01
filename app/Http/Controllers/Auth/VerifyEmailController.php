<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomEmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(CustomEmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->getUserModel();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('status', 'Your email is already verified. You can now log in to your account.');
        }

        $user->markEmailAsVerified();

        return redirect()->route('login')
            ->with('status', 'Your email has been verified successfully! You can now log in to your account.');
    }
}
