<?php

use App\Http\Controllers\CalendarGeneratorController;
use App\Http\Controllers\ProfileController;
use App\Models\IcsEvent;
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

Route::get('/start', function () {
    return Inertia::render('Start');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/generate', function () {
        return Inertia::render('Generate', [
            'serverErrorMessage' => request('serverErrorMessage'),
            'serverSuccess' => request('serverSuccess'),
            'eventId' => request('eventId'),
            'eventSecret' => request('eventSecret')
        ]);
    })->name('generate');

    Route::get('/checkout', function() {
        $checkout = request()->user()->subscribe('341208')->url();
        return Inertia::render('Dashboard', compact('checkout'));
    })->name('checkout');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::view('/ics', 'mail.ics_success', ['ics' => IcsEvent::find(25)]);
Route::view('/icserror', 'mail.ics_error', ['ics' => IcsEvent::find(11)]);

Route::get('event/download/{id}/{secret}', [CalendarGeneratorController::class, 'downloadEvent'])->name('event.download');

require __DIR__ . '/auth.php';
