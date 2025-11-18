<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\Auth\CompleteStudentProfileController;
use App\Http\Controllers\Auth\CompleteFacultyProfileController;
use App\Http\Controllers\ResearchController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

/*
 |---------------------------------------------------------------------------
 | Authenticated routes
 |---------------------------------------------------------------------------
 |
 | Profile completion, dashboard, resource routes and log endpoints are
 | protected by the auth middleware.
 |
 */
Route::middleware(['auth'])->group(function () {
    // Profile completion (authentication flow for new users)
    // Student profile completion
    Route::get('/student/profile/complete', [CompleteStudentProfileController::class, 'show'])->name('student.profile.complete');
    Route::post('/student/profile/complete', [CompleteStudentProfileController::class, 'store'])->name('student.profile.complete.store');

    // Faculty profile completion
    Route::get('/faculty/profile/complete', [CompleteFacultyProfileController::class, 'show'])->name('faculty.profile.complete');
    Route::post('/faculty/profile/complete', [CompleteFacultyProfileController::class, 'store'])->name('faculty.profile.complete.store');

    // Dashboard (controller)
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Faculties
    Route::resource('faculties', FacultyController::class);
    Route::post('faculties/bulk-destroy', [FacultyController::class, 'bulkDestroy'])->name('faculties.bulk-destroy');
    Route::get('faculties/export', [FacultyController::class, 'export'])->name('faculties.export');
    Route::get('faculties/statistics', [FacultyController::class, 'statistics'])->name('faculties.statistics');

    // Research (resource + extra actions present in your ResearchController)
    Route::resource('research', ResearchController::class);
    Route::post('research/bulk-destroy', [ResearchController::class, 'bulkDestroy'])->name('research.bulk-destroy');
    Route::get('research/export', [ResearchController::class, 'export'])->name('research.export');
    Route::get('research/statistics', [ResearchController::class, 'statistics'])->name('research.statistics');
    Route::post('research/{research}/archive', [ResearchController::class, 'archive'])->name('research.archive');
    Route::post('research/{research}/restore', [ResearchController::class, 'restore'])->name('research.restore');
    Route::get('research/{research}/download', [ResearchController::class, 'downloadPdf'])->name('research.download');

    // Logs (admin-only via controller authorization)
    Route::prefix('logs')->group(function () {
        Route::get('/research-access', [LogsController::class, 'researchAccess'])->name('logs.research-access');
        Route::get('/keyword-search', [LogsController::class, 'keywordSearch'])->name('logs.keyword-search');
        Route::get('/user-audits', [LogsController::class, 'userAudits'])->name('logs.user-audits');
        Route::get('/faculty-audits', [LogsController::class, 'facultyAudits'])->name('logs.faculty-audits');
        Route::get('/research-entries', [LogsController::class, 'researchEntries'])->name('logs.research-entries');

        // Stats endpoints
        Route::get('/stats/top-accessed-research', [LogsController::class, 'topAccessedResearch'])->name('logs.stats.top-accessed-research');
        Route::get('/stats/top-keywords', [LogsController::class, 'topKeywords'])->name('logs.stats.top-keywords');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
