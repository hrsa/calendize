<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('landing page is displayed to guests and redirects users', function () {
    get(route('home'))->assertOk();

    $user = User::factory()->create();
    actingAs($user)->get(route('home'))->assertRedirectToRoute('generate');
});

test('guest generation page is displayed to guests and redirects users', function () {
    get(route('try'))->assertOk();

    $user = User::factory()->create();
    actingAs($user)->get(route('try'))->assertRedirectToRoute('generate');
});

test('pricing page is displayed to guests and redirects users', function () {
    get(route('pricing'))->assertOk();

    $user = User::factory()->create();
    actingAs($user)->get(route('pricing'))->assertRedirectToRoute('dashboard');
});

test('terms of service and privacy policy pages are displayed', function () {
    get(route('terms-of-service'))->assertOk();
    get(route('privacy-policy'))->assertOk();
});
