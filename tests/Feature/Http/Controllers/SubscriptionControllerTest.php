<?php

use App\Enums\LemonSqueezyProduct;
use App\Models\User;
use App\Services\UserService;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test("guests can't access subscription links", function () {
    get(route('subscriptions.get-modification-data'))->assertRedirect();
    post(route('subscriptions.subscribe'), ['type' => 'beginner'])->assertRedirect();
    post(route('subscriptions.cancel'))->assertRedirect();
    post(route('subscriptions.swap'), ['newSubscription' => 'beginner'])->assertRedirect();
});

test('subscription links are created for users', function () {

    $userServiceMock = Mockery::mock(UserService::class);
    $userServiceMock->shouldReceive('createSubscriptionLink')->andReturn('https://payment.link/subscription');
    $this->app->instance(UserService::class, $userServiceMock);

    $user = User::factory()->create();
    $subscription = collect(LemonSqueezyProduct::subscriptions())->random();

    actingAs($user)
        ->get(route('subscriptions.get-modification-data'))->assertBadRequest();
    actingAs($user)
        ->post(route('subscriptions.subscribe'), ['type' => $subscription->value])
        ->assertOk()->assertJsonFragment(['https://payment.link/subscription']);

});
