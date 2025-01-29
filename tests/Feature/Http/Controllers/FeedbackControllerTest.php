<?php

use App\Models\Feedback;
use App\Models\IcsEvent;
use App\Models\User;
use App\Notifications\Telegram\Admin\FeedbackReceived;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Carbon;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

beforeEach(function () {

    Notification::fake();

    $this->secretToken = 'testing-secret-token';
    config(['services.telegram-bot-api.header-token' => $this->secretToken]);
});

test('feedback from author is processed', function () {
    $user = User::factory()->create([
        'credits'           => 5,
        'failed_requests'   => 6,
        'email_verified_at' => Carbon::now(),
    ]);

    $icsEvent = IcsEvent::factory()->icsProcessed()->create(['user_id' => $user->id]);

    $feedback = Feedback::first();

    expect($feedback)->toBeNull();

    actingAs($user)->postJson(route('feedback'), [
        'ics_event_id' => $icsEvent->id,
        'like' => true,
        'data' => 'test',
    ])->assertCreated();

    $feedback = Feedback::first();
    expect($feedback)->not->toBeNull()
        ->and($feedback->like)->toBeTrue()
        ->and($feedback->data)->toBe('test')
        ->and($feedback)->ics_event_id->toBe($icsEvent->id);

    Notification::assertSentTo(new AnonymousNotifiable(), FeedbackReceived::class);
});

test('feedback not from authors is refused', function () {
    $user1 = User::factory()->create([
        'credits'           => 5,
        'failed_requests'   => 6,
        'email_verified_at' => Carbon::now(),
    ]);

    $user2 = User::factory()->create([
        'credits'           => 5,
        'failed_requests'   => 6,
        'email_verified_at' => Carbon::now(),
    ]);

    $icsEvent = IcsEvent::factory()->icsProcessed()->create(['user_id' => $user2->id]);

    actingAs($user1)->postJson(route('feedback'), [
        'ics_event_id' => $icsEvent->id,
        'like' => true,
        'data' => 'test',
    ])->assertForbidden();

    Notification::assertNotSentTo(new AnonymousNotifiable(), FeedbackReceived::class);
});

test('feedback from anonymous users is not processed', function () {
    $icsEvent = IcsEvent::factory()->icsProcessed()->create();

    postJson(route('feedback'), [
        'ics_event_id' => $icsEvent->id,
        'like' => true,
        'data' => 'test',
    ])->assertUnauthorized();

    Notification::assertNotSentTo(new AnonymousNotifiable(), FeedbackReceived::class);
});

test('feedback data is validated', function () {

    $user = User::factory()->create([
        'credits'           => 5,
        'failed_requests'   => 6,
        'email_verified_at' => Carbon::now(),
    ]);

    $icsEvent = IcsEvent::factory()->icsProcessed()->create(['user_id' => $user->id]);

    actingAs($user)->postJson(route('feedback'), [
        'ics_event_id' => $icsEvent->id,
        'data' => 'test',
    ])->assertUnprocessable()->assertJsonValidationErrors(['like']);

    actingAs($user)->postJson(route('feedback'), [
        'ics_event_id' => $icsEvent->id,
        'like' => true,
    ])->assertUnprocessable()->assertJsonValidationErrors(['data']);

    actingAs($user)->postJson(route('feedback'), [
        'ics_event_id' => $icsEvent->id,
        'like' => 'string',
        'data' => true,
    ])->assertUnprocessable()->assertJsonValidationErrors(['like', 'data']);

    Notification::assertNotSentTo(new AnonymousNotifiable(), FeedbackReceived::class);
});

