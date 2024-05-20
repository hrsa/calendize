<?php

use App\Http\Controllers\CalendarGeneratorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('if-not-guest-redirect-to:generate')->group(function () {
    Route::get('/', fn () => Inertia::render('Landing'))->name('home');
    Route::get('/try', fn () => Inertia::render('Try'))->name('try');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/how-to-use', fn () => Inertia::render('HowToUse'))->name('how-to-use');
    Route::get('/my-events', [CalendarGeneratorController::class, 'usersEvents'])->name('my-events');

    Route::middleware('verified')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/generate', function () {
            return Inertia::render('Generate', [
                'serverErrorMessage' => request('serverErrorMessage'),
                'serverSuccess' => request('serverSuccess'),
                'eventId' => request('eventId'),
                'eventSecret' => request('eventSecret'),
            ]);
        })->name('generate');
    });
});

Route::get('event/download/{id}/{secret}', [CalendarGeneratorController::class, 'downloadEvent'])->name('event.download');

Route::get('pricing', fn () => Inertia::render('Pricing'))
    ->middleware('if-not-guest-redirect-to:dashboard')->name('pricing');
Route::get('privacy-policy', fn () => Inertia::render('PrivacyPolicy'))->name('privacy-policy');
Route::get('terms-of-service', fn () => Inertia::render('TermsOfService'))->name('terms-of-service');

require __DIR__ . '/auth.php';

Route::fallback(fn () => redirect()->route('home'));
