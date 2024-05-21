<?php

/** @noinspection AnonymousFunctionStaticInspection */

use App\Http\Controllers\Auth\SocialiteLoginController;
use App\Http\Controllers\CalendarGeneratorController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/guest-generate-calendar', [CalendarGeneratorController::class, 'guestGenerate'])->name('guest-generate-calendar');
Route::post('/generate-calendar', [CalendarGeneratorController::class, 'generate'])->middleware('auth:sanctum')->name('generate-calendar');

Route::group([
    'as'         => 'socialite.',
    'middleware' => 'web',
    'controller' => SocialiteLoginController::class,
], function () {
    Route::get('/google/redirect', 'redirectToGoogle')->name('google.redirect');
    Route::get('/google/callback', 'handleGoogleCallback')->name('google.callback');
    Route::get('/linkedin/redirect', 'redirectToLinkedin')->name('linkedin.redirect');
    Route::get('/linkedin/callback', 'handleLinkedinCallback')->name('linkedin.callback');
});

Route::group([
    'prefix'     => '/users',
    'controller' => UserController::class,
], function () {
    Route::post('/check', 'checkEmail')->name('user.check-email');
    Route::post('/hide-password-reminder', 'hidePasswordReminder')->middleware('auth')->name('hide-password-reminder');
});

Route::group([
    'prefix'     => '/subscriptions',
    'as'         => 'subscriptions.',
    'middleware' => 'auth', 'verified',
    'controller' => SubscriptionController::class,
], function () {
    Route::post('/subscribe', 'subscribe')->name('subscribe');
    Route::get('/get-modification-data', 'getModificationData')->name('get-modification-data');
    Route::post('/cancel', 'cancel')->name('cancel');
    Route::post('/swap', 'swap')->name('swap');
});
