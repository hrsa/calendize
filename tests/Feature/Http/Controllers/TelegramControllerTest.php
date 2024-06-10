<?php

use App\Enums\TelegramCallback;
use App\Enums\TelegramCommand;
use App\Jobs\GenerateCalendarJob;
use App\Models\IcsEvent;
use App\Models\User;
use App\Notifications\Telegram\Admin\UnknownMessageReceived;
use App\Notifications\Telegram\ReplyToUnknownUser;
use App\Notifications\Telegram\User\CreditsRemaining;
use App\Notifications\Telegram\User\CustomMessage;
use App\Notifications\Telegram\User\IcsEventValid;
use App\Notifications\Telegram\User\MyEvents;
use App\Notifications\Telegram\User\ReplyToUnknownCommand;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

beforeEach(function () {

    Notification::fake();
    Queue::fake();

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

    $this->telegramCallbackQueryData = [
        'update_id'      => 123456789,
        'callback_query' => [
            'id'   => '1234567890:ABCDEFGH',
            'from' => [
                'id'            => $this->tgUserId,
                'is_bot'        => false,
                'first_name'    => 'John',
                'last_name'     => 'Doe',
                'username'      => 'johndoe',
                'language_code' => 'en-US',
            ],
            'message' => [
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
                'text' => '/start',
            ],
            'data' => null,
        ],
    ];
});

test('users can add a telegram account, guests cannot', function () {

    get(route('telegram.connect'))->assertRedirectToRoute('login');

    $user = User::factory()->create();
    $telegramId = fake()->randomNumber(6);

    expect($user->telegram_id)->toBeNull();

    actingAs($user)->get(route('telegram.connect'))->assertRedirectToRoute('login');

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

    Notification::assertSentTo($user, CustomMessage::class);
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

test('telegram messages from users are processed', function () {
    $user = User::factory()->create(['telegram_id' => $this->tgUserId, 'send_tg_notifications' => true]);
    Redis::spy();

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo(new AnonymousNotifiable(), UnknownMessageReceived::class);
    Notification::assertSentTo($user, ReplyToUnknownCommand::class);
    Notification::assertCount(2);
});

test("unknown command doesn't break anything", function () {
    Redis::spy();
    $user = User::factory()->create(['telegram_id' => $this->tgUserId, 'send_tg_notifications' => true, 'credits' => 5]);
    $this->telegramWebHookData['message']['text'] = '/aclearlyFakeCommand';

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo(new AnonymousNotifiable(), UnknownMessageReceived::class);
    Notification::assertSentTo($user, ReplyToUnknownCommand::class);
    Notification::assertCount(2);
});

test('credits command from users is processed', function () {
    $user = User::factory()->create(['telegram_id' => $this->tgUserId, 'send_tg_notifications' => true, 'credits' => 5]);
    $this->telegramWebHookData['message']['text'] = TelegramCommand::MyCredits->value;

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CreditsRemaining::class);
    Notification::assertCount(1);
});

test('my events command from users is processed', function () {
    $user = User::factory()->create(['telegram_id' => $this->tgUserId, 'send_tg_notifications' => true, 'credits' => 5]);
    $this->telegramWebHookData['message']['text'] = TelegramCommand::MyEvents->value;

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, MyEvents::class);
    Notification::assertCount(1);
});

test('"notify me" and "don\'t notify me" commands from users are processed', function () {
    $user = User::factory()->create(['telegram_id' => $this->tgUserId, 'send_tg_notifications' => true, 'credits' => 5]);
    $this->telegramWebHookData['message']['text'] = TelegramCommand::DontNotifyMe->value;

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CustomMessage::class);
    Notification::assertCount(1);
    $user->refresh();
    expect($user->send_tg_notifications)->toBeFalsy();

    $this->telegramWebHookData['message']['text'] = TelegramCommand::NotifyMe->value;

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CustomMessage::class);
    Notification::assertCount(2);
    $user->refresh();
    expect($user->send_tg_notifications)->toBeTruthy();
});

test('my events callback command from users is processed', function () {
    $user = User::factory()->create(['telegram_id' => $this->tgUserId, 'send_tg_notifications' => true, 'credits' => 5]);
    IcsEvent::factory()->icsProcessed()->count(15)->create(['user_id' => $user->id]);
    $this->telegramCallbackQueryData['callback_query']['data'] = TelegramCallback::GetEventsOnPage->value . '=2';

    postJson(route('telegram.process-webhook'), $this->telegramCallbackQueryData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, MyEvents::class);
    Notification::assertCount(1);
});

test('get event callback command from users is processed', function () {
    $user = User::factory()->create(['telegram_id' => $this->tgUserId, 'send_tg_notifications' => true, 'credits' => 5]);

    $this->telegramCallbackQueryData['callback_query']['data'] = TelegramCallback::GetEvent->value . '=1';

    postJson(route('telegram.process-webhook'), $this->telegramCallbackQueryData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CustomMessage::class);
    Notification::assertCount(1);

    IcsEvent::factory()->icsError()->create(['user_id' => $user->id]);

    postJson(route('telegram.process-webhook'), $this->telegramCallbackQueryData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CustomMessage::class);
    Notification::assertCount(2);

    IcsEvent::factory()->icsProcessed()->count(5)->create(['user_id' => $user->id]);
    $this->telegramCallbackQueryData['callback_query']['data'] = TelegramCallback::GetEvent->value . '=5';

    postJson(route('telegram.process-webhook'), $this->telegramCallbackQueryData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, IcsEventValid::class);
    Notification::assertCount(3);
});

test('user can calendize its last message by using a command', function () {
    $user = User::factory()->create(['telegram_id' => $this->tgUserId, 'send_tg_notifications' => true, 'credits' => 5]);

    $this->telegramWebHookData['message']['text'] = TelegramCommand::Calendize->value;

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CustomMessage::class);
    Notification::assertCount(1);
    Queue::assertNothingPushed();

    $this->telegramWebHookData['message']['text'] = TelegramCommand::Calendize->value . ' a long description of the event';

    $user->update(['credits' => 0]);

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CustomMessage::class);
    Notification::assertCount(2);
    Queue::assertNothingPushed();

    $user->update(['credits' => 3]);

    postJson(route('telegram.process-webhook'), $this->telegramWebHookData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CustomMessage::class);
    Notification::assertCount(3);
    Queue::assertPushed(GenerateCalendarJob::class);
});

test('user can calendize its last message by using a callback', function () {
    $user = User::factory()->create(['telegram_id' => $this->tgUserId, 'send_tg_notifications' => true, 'credits' => 5]);

    $this->telegramCallbackQueryData['callback_query']['data'] = TelegramCallback::Calendize->value . '=' . $this->tgUserId;

    Redis::shouldReceive('get')->once()->with($this->tgUserId)->andReturn(null);

    postJson(route('telegram.process-webhook'), $this->telegramCallbackQueryData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CustomMessage::class);
    Notification::assertCount(1);
    Queue::assertNothingPushed();

    Redis::shouldReceive('get')->once()->with($this->tgUserId)->andReturn('a long string about the event to calendize');

    postJson(route('telegram.process-webhook'), $this->telegramCallbackQueryData, ['x-telegram-bot-api-secret-token' => $this->secretToken])->assertOk();
    Notification::assertSentTo($user, CustomMessage::class);
    Notification::assertCount(2);
    Queue::assertPushed(GenerateCalendarJob::class);
});
