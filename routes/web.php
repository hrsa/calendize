<?php

use App\Http\Controllers\CalendarGeneratorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('if-not-guest-redirect-to:generate')->group(function () {
    Route::inertia('/', 'Landing')->name('home');
    Route::inertia('/try', 'Try')->name('try');
});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/my-events', [CalendarGeneratorController::class, 'usersEvents'])->name('my-events');
    Route::inertia('/how-to-use', 'HowToUse')->name('how-to-use');

    Route::middleware('verified')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::inertia('/generate', 'Generate', [
            'serverErrorMessage' => request('serverErrorMessage'),
            'serverSuccess'      => request('serverSuccess'),
            'eventId'            => request('eventId'),
            'eventSecret'        => request('eventSecret'),
        ])->name('generate');
    });
});

Route::get('event/download/{id}/{secret}', [CalendarGeneratorController::class, 'downloadEvent'])->name('event.download');
Route::inertia('pricing', 'Pricing')->middleware('if-not-guest-redirect-to:dashboard')->name('pricing');
Route::inertia('privacy-policy', 'PrivacyPolicy')->name('privacy-policy');
Route::inertia('terms-of-service', 'TermsOfService')->name('terms-of-service');

require __DIR__ . '/auth.php';

Route::fallback(fn () => redirect()->route('home'));
