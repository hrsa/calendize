<?php /** @noinspection AnonymousFunctionStaticInspection */

use App\Http\Controllers\CalendarGeneratorController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/guest-generate-calendar', [CalendarGeneratorController::class, 'guestGenerate'])->name('guest-generate-calendar');
Route::post('/generate-calendar', [CalendarGeneratorController::class, 'generate'])->middleware('auth:sanctum')->name('generate-calendar');

Route::group(['prefix' => '/users'], function (){

    Route::post('/signup', [UserController::class, 'signup'])->name('user.signup');
    Route::post('/login', [UserController::class, 'login'])->name('user.login');
    Route::post('/check', [UserController::class, 'checkEmail'])->name('user.check-email');
});

Route::group([
    'prefix' => '/subscriptions',
    'as' => 'subscriptions.',
    'middleware' => 'auth', 'verified'
], function (){
   Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
   Route::get('/get-modification-data', [SubscriptionController::class, 'getModificationData'])->name('get-modification-data');
   Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
   Route::post('/swap', [SubscriptionController::class, 'swap'])->name('swap');
});


