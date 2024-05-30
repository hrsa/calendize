<?php

use App\Models\User;
use App\Notifications\Telegram\Admin\UnknownMessageReceived;
use App\Notifications\Telegram\ReplyToUnknownUser;
use App\Notifications\Telegram\User\CustomMesssage;
use App\Services\TelegramService;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

beforeEach(function () {

    Notification::fake();

    $this->secretToken = 'testing-secret-token';
    config(['services.telegram-bot-api.header-token' => $this->secretToken]);

    $this->tgUserId = 123456789;
    $this->tgMessage = 'hello';

    $this->telegramWebHookData = [
        'update_id' => 123456789,
        'message'   => [
            'message_id' => 123,
            'from'       => [
                'id'            => $this->tgUserId,
                'is_bot'        => false,
                'first_name'    => 'John',
                'last_name'     => 'Doe',
                'username'      => 'johndoe',
                'language_code' => 'en-US',
            ],
            'chat' => [
                'id'         => $this->tgUserId,
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'username'   => 'johndoe',
                'type'       => 'private',
            ],
            'date' => 1612457896,
            'text' => $this->tgMessage,
        ],
    ];
});

test('users can add a telegram account, guests cannot', function () {

    get(route('telegram.connect'))->assertRedirectToRoute('login');

    $user = User::factory()->create();
    $telegramId = fake()->randomNumber(6);

    expect($user->telegram_id)->toBeNull();

    actingAs($user)->get(route('telegram.connect'))->assertRedirectToRoute('home');

    actingAs($user)->get(route('telegram.connect',
        ['tgid' => base64_encode((string) $telegramId)]))
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

test('telegram webhook is processed only with a valid secret token header', function () {

    Notification::assertCount(0);
    postJson(route('telegram.process-webhook'), $this->telegramWebHookData)->assertOk();
    Notification::assertNothingSent();

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo(new AnonymousNotifiable(), UnknownMessageReceived::class);
    Notification::assertSentTo(new AnonymousNotifiable(), ReplyToUnknownUser::class);
    Notification::assertCount(2);
});
