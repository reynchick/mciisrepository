<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\CompiledReportController;
use App\Http\Controllers\Settings\ProfileController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Profile completion routes (before 'verified' middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/complete', [ProfileController::class, 'showComplete'])
        ->name('profile.complete');
    
    Route::post('/profile/complete', [ProfileController::class, 'storeComplete'])
        ->name('profile.complete.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    
    // Faculty routes
    Route::resource('faculties', FacultyController::class);
    Route::post('faculties/bulk-destroy', [FacultyController::class, 'bulkDestroy'])->name('faculties.bulk-destroy');
    Route::get('faculties/export', [FacultyController::class, 'export'])->name('faculties.export');
    Route::get('faculties/statistics', [FacultyController::class, 'statistics'])->name('faculties.statistics');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
