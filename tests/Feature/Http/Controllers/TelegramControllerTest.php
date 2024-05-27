<?php

use App\Models\User;
use App\Notifications\Telegram\User\CustomMesssage;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('users can add a telegram account, guests cannot', function () {

    Notification::fake();

    get(route('telegram.connect'))->assertRedirectToRoute('login');

    $user = User::factory()->create();
    $telegramId = fake()->randomNumber(6);

    expect($user->telegram_id)->toBeNull();

    actingAs($user)->get(route('telegram.connect'))->assertRedirectToRoute('home');

    actingAs($user)->get(route('telegram.connect',
        ['tgid' => base64_encode($telegramId)]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Generate')
            ->has('serverSuccess')
            ->where('serverSuccess', 'Your Telegram account is successfully connected!')
            ->where('serverErrorMessage', null)
            ->where('eventId', null)
            ->where('eventSecret', null)
        );

    expect($user->telegram_id)->toEqual($telegramId);

    Notification::assertSentTo($user, CustomMesssage::class);
});
