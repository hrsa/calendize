<?php

/** @noinspection AnonymousFunctionStaticInspection */

use App\Http\Controllers\Auth\SocialiteLoginController;
use App\Http\Controllers\CalendarGeneratorController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/guest-generate-calendar', [CalendarGeneratorController::class, 'guestGenerate'])->name('guest-generate-calendar');
Route::post('/generate-calendar', [CalendarGeneratorController::class, 'generate'])->middleware('auth:sanctum')->name('generate-calendar');

Route::group(['as' => 'socialite.', 'middleware' => 'web'], function () {
    Route::get('/google/redirect', [SocialiteLoginController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [SocialiteLoginController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::get('/linkedin/redirect', [SocialiteLoginController::class, 'redirectToLinkedin'])->name('linkedin.redirect');
    Route::get('/linkedin/callback', [SocialiteLoginController::class, 'handleLinkedinCallback'])->name('linkedin.callback');
});

Route::group(['prefix' => '/users'], function () {
    Route::post('/check', [UserController::class, 'checkEmail'])->name('user.check-email');
});

Route::group([
    'prefix' => '/subscriptions',
    'as' => 'subscriptions.',
    'middleware' => 'auth', 'verified',
], function () {
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::get('/get-modification-data', [SubscriptionController::class, 'getModificationData'])->name('get-modification-data');
    Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
    Route::post('/swap', [SubscriptionController::class, 'swap'])->name('swap');
});
