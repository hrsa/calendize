<?php

/** @noinspection AnonymousFunctionStaticInspection */

use App\Http\Controllers\Auth\SocialiteLoginController;
use App\Http\Controllers\CalendarGeneratorController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'controller' => CalendarGeneratorController::class,
], function () {
    Route::post('/guest-generate-calendar', 'guestGenerate')->name('guest-generate-calendar');
    Route::post('/generate-calendar', 'generate')->middleware('auth:sanctum')->name('generate-calendar');
});

Route::group([
    'as'         => 'socialite.',
    'middleware' => 'web',
    'controller' => SocialiteLoginController::class,
], function () {
    Route::get('/google/redirect', 'redirectToGoogle')->name('google.redirect');
    Route::get('/google/callback', 'handleGoogleCallback')->name('google.callback');
    Route::get('/linkedin/redirect', 'redirectToLinkedin')->name('linkedin.redirect');
    Route::get('/linkedin/callback', 'handleLinkedinCallback')->name('linkedin.callback');
    Route::get('/twitter/redirect', 'redirectToTwitter')->name('twitter.redirect');
    Route::get('/twitter/callback', 'handleTwitterCallback')->name('twitter.callback');
});

Route::group([
    'prefix'     => '/users',
    'as'         => 'users.',
    'controller' => UserController::class,
], function () {
    Route::post('/check', 'checkEmail')->name('check-email');
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
Route::group([
    'prefix'     => '/telegram',
    'as'         => 'telegram.',
    'controller' => TelegramController::class,
], function () {
    Route::post('/hook', 'processWebhook')->name('process-webhook');
    Route::get('/connect', 'connectTelegram')->middleware('web', 'auth')->name('connect');
});
