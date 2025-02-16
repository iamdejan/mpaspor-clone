<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PassportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [PassportController::class, 'index'])->name('dashboard');
    Route::get('/passport/apply', [PassportController::class, 'createApplyRequest'])->name('passport.apply');
    Route::get('/passport/{workflow_id}/first-page', [PassportController::class, 'viewFirstPageForm'])->name('passport.view-first-page');
});

require __DIR__.'/auth.php';
