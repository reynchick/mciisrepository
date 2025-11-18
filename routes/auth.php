<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

// Guest routes (login and Google OAuth)
Route::middleware('guest')->group(function () {
    // Login page (Google SSO only)
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // Google OAuth routes
    Route::get('auth/google', [GoogleAuthController::class, 'redirect'])
        ->name('auth.google');

    Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('auth.google.callback');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
