<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    actingAs($user)->get('/confirm-password')->assertOk();
});

test('password can be confirmed', function () {
    $user = User::factory()->create();

    actingAs($user)->post('/confirm-password', [
        'password' => 'password',
    ])->assertRedirect()->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->create();

    actingAs($user)->post('/confirm-password', [
        'password' => 'wrong-password',
    ])->assertSessionHasErrors();
});
