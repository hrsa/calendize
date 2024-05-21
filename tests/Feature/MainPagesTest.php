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
