<?php

use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    Redis::spy();
});

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

test('terms of service and privacy policy pages are displayed', function (string $route) {
    get(route($route))->assertOk();
})->with(['route' => 'terms-of-service'], ['route' => 'privacy-policy']);

test('Horizon and Pulse pages are only accessible to admin', function (string $uri) {
    get($uri)->assertRedirectToRoute('home');

    $user = User::factory()->create();
    actingAs($user)->get($uri)->assertRedirectToRoute('how-to-use');

    $admin = User::factory()->create(['email' => config('app.admin.email')]);
    actingAs($admin)->get($uri)->assertOk();
})->with(['uri' => '/horizon'], ['uri' => '/pulse']);
