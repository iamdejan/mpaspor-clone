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

    // first page
    Route::get('/passport/{workflow_id}/first-page', [PassportController::class, 'viewFirstPageForm'])->name('passport.first-page.view');
    Route::post('/passport/{workflow_id}/first-page', [PassportController::class, 'submitFirstPageForm'])->name('passport.first-page.submit');

    // second page
    Route::get('/passport/{workflow_id}/second-page', [PassportController::class, 'viewSecondPageForm'])->name('passport.second-page.view');
});

require __DIR__.'/auth.php';
