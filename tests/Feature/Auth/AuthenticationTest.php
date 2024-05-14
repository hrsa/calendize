<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('home page can be rendered for guests', function () {
    $this->assertGuest();

    get(route('home'))->assertOk();
});

test('authenticated users get redirected from home page', function () {
    $user = User::factory()->create();

    actingAs($user)->get(route('home'))->assertRedirectToRoute('generate');
});

test('guest generation page can be rendered for guests', function () {
    $this->assertGuest();

    get(route('try'))->assertOk();
});

test('authenticated users get redirected from guest generation page', function () {
    $user = User::factory()->create();

    actingAs($user)->get(route('try'))->assertRedirectToRoute('generate');
});

test('login screen can be rendered', function () {
    get('/login')->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    actingAs($user)->post('/logout')->assertRedirect('/');

    $this->assertGuest();
});
