<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('Guest-only pages are displayed to guests and redirect users',
    function (string $route, string $component, string $redirectRoute) {
        get(route($route))->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($component));

        $user = User::factory()->create();
        actingAs($user)->get(route($route))->assertRedirectToRoute($redirectRoute);
    })->with([
        [
            'route'         => 'home',
            'component'     => 'Landing',
            'redirectRoute' => 'generate',
        ],
        [
            'route'         => 'try',
            'component'     => 'Try',
            'redirectRoute' => 'generate',
        ],
        [
            'route'         => 'pricing',
            'component'     => 'Pricing',
            'redirectRoute' => 'dashboard',
        ],
    ]);

test('terms of service and privacy policy pages are displayed', function () {
    get(route('terms-of-service'))->assertOk();
    get(route('privacy-policy'))->assertOk();
});

test('Horizon and Pulse pages are only accessible to admin', function () {
    get('/horizon')->assertRedirectToRoute('home');
    get('/pulse')->assertRedirectToRoute('home');

    $user = User::factory()->create();

    actingAs($user)->get('/pulse')->assertRedirectToRoute('how-to-use');
    actingAs($user)->get('/horizon')->assertRedirectToRoute('how-to-use');

    $admin = User::factory()->create(['email' => config('app.admin.email')]);

    actingAs($admin)->get('/pulse')->assertOk();
    actingAs($admin)->get('/horizon')->assertForbidden(); //because no Redis is running in test environment
});
