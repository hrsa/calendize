<?php

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('registration screen can be rendered', function () {

    get('/register')
        ->assertStatus(200);
});

test('new users can register', function () {
    post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('generate', absolute: false));

    $this->assertAuthenticated();
});
