<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\CompiledReportController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified', 'require.password.change'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    
    // Faculty routes
    Route::resource('faculties', FacultyController::class);
    Route::post('faculties/bulk-destroy', [FacultyController::class, 'bulkDestroy'])->name('faculties.bulk-destroy');
    Route::get('faculties/export', [FacultyController::class, 'export'])->name('faculties.export');
    Route::get('faculties/statistics', [FacultyController::class, 'statistics'])->name('faculties.statistics');

    // Compiled Report routes
    Route::resource('compiled-reports', CompiledReportController::class);
    Route::get('compiled-reports/{compiledReport}/download', [
        CompiledReportController::class, 
        'download'
    ])->name('compiled-reports.download');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
