<?php

use App\Models\User;
use App\Services\UserService;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('users can access dashboard, guests cannot', function () {

    get(route('dashboard'))->assertRedirect();

    $user = User::factory()->create();

    $userServiceMock = Mockery::mock(UserService::class);
    $userServiceMock->shouldReceive('createTopUpCreditsLink')->andReturn('https://payment.link/topup');

    $this->app->instance(UserService::class, $userServiceMock);

    actingAs($user)->get(route('dashboard'))->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('buyCreditsLink')
            ->where('buyCreditsLink', 'https://payment.link/topup')
            ->where('paymentConfirmation', null)
        );
});

test('dashboard popups are generated correctly from route parameters', function () {

    $user = User::factory()->create();

    $userServiceMock = Mockery::mock(UserService::class);
    $userServiceMock->shouldReceive('createTopUpCreditsLink')->andReturn('https://payment.link/topup');
    $this->app->instance(UserService::class, $userServiceMock);

    actingAs($user)->get(route('dashboard', ['payment' => 'credits']))->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('buyCreditsLink')
            ->where('buyCreditsLink', 'https://payment.link/topup')
            ->has('paymentConfirmation')
            ->where('paymentConfirmation.imageAlt', 'Calendize')
            ->where('paymentConfirmation.imageSrc', '/credits.png')
            ->where('paymentConfirmation.title', 'Your payment was successful!')
            ->where('paymentConfirmation.content', "Thanks! I'm looking forward to working with you!")
        );

    actingAs($user)->get(route('dashboard', ['payment' => 'beginner']))->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('buyCreditsLink')
            ->where('buyCreditsLink', 'https://payment.link/topup')
            ->has('paymentConfirmation')
            ->where('paymentConfirmation.imageAlt', 'Calendize')
            ->where('paymentConfirmation.imageSrc', '/beginner.png')
            ->where('paymentConfirmation.title', 'beginner')
            ->where('paymentConfirmation.content', 'Thanks! beginner now!')
        );

    actingAs($user)->get(route('dashboard', ['payment' => 'classic']))->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('buyCreditsLink')
            ->where('buyCreditsLink', 'https://payment.link/topup')
            ->has('paymentConfirmation')
            ->where('paymentConfirmation.imageAlt', 'Calendize')
            ->where('paymentConfirmation.imageSrc', '/classic.png')
            ->where('paymentConfirmation.title', 'classic')
            ->where('paymentConfirmation.content', 'Thanks! classic now!')
        );

    actingAs($user)->get(route('dashboard', ['payment' => 'power']))->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('buyCreditsLink')
            ->where('buyCreditsLink', 'https://payment.link/topup')
            ->has('paymentConfirmation')
            ->where('paymentConfirmation.imageAlt', 'Calendize')
            ->where('paymentConfirmation.imageSrc', '/power.png')
            ->where('paymentConfirmation.title', 'power')
            ->where('paymentConfirmation.content', 'Thanks! power now!')
        );
});
