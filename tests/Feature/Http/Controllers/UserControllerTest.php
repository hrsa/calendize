<?php

use App\Models\User;
use Carbon\Carbon;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

test('emails are properly verified', function () {
    $user = User::factory()->create();

    post(route('users.check-email'), ['email' => 'random@email.com'])->assertNoContent();
    post(route('users.check-email'), ['email' => $user->email])->assertForbidden();

});

test('user can hide the password reminder', function () {
    $user = User::factory()->create();
    expect($user->hide_pw_reminder)->toBeNull();

    actingAs($user)->post(route('users.hide-password-reminder'));

    expect($user->refresh()->hide_pw_reminder)->toEqual(today());
    Carbon::setTestNow(now()->addDays(10));

    actingAs($user)->post(route('users.hide-password-reminder'));
    expect($user->refresh()->hide_pw_reminder)->toEqual(today());

});
