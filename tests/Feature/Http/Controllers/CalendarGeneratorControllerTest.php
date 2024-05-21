<?php

use App\Jobs\GenerateCalendarJob;
use App\Models\IcsEvent;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('validates icsEvent requests', function () {
    $email         = fake()->word();
    $calendarEvent = '';

    $response = post(route('guest-generate-calendar'), compact('email', 'calendarEvent'));

    $response->assertStatus(302);

    $errors = session('errors')->getBag('default')->getMessages();

    expect($errors)->toHaveKeys(['calendarEvent', 'email'])
        ->and($errors['calendarEvent'][0])->toEqual("The calendar event field can't be empty.")
        ->and($errors['email'][0])->toEqual('The email field must be a valid email address.');
});

test('guest can generate icsEvent', function () {
    Event::fake();
    Notification::fake();
    Queue::fake();

    $this->assertGuest();

    $user = User::find(1);
    $this->assertNull($user);

    $email         = fake()->unique()->safeEmail();
    $calendarEvent = fake()->text(150);
    $timeZone      = fake()->timezone();

    post(route('guest-generate-calendar'),
        compact('email', 'calendarEvent', 'timeZone'))->assertStatus(200)
        ->assertJson(['reply' => 'ok']);

    $user = User::find(1);
    expect($user)->not()->toBeNull()
        ->and($user->email)->toBe($email)
        ->and($user->email_verified_at)->toBeNull()
        ->and($user->credits)->toBe(1)
        ->and($user->has_password)->toBeFalsy();

    $icsEvent = IcsEvent::find(1);

    expect($icsEvent)->not()->toBeNull()
        ->and($icsEvent->user_id)->toBe($user->id)
        ->and($icsEvent->secret)->not()->toBeNull()
        ->and($icsEvent->timezone)->toBe($timeZone)
        ->and($icsEvent->prompt)->toBe($calendarEvent)
        ->and($icsEvent->ics)->toBeNull()
        ->and($icsEvent->error)->toBeNull();

    Queue::assertNothingPushed();

    Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification, $channels) use ($user, &$verificationUrl) {
        $mailMessage = $notification->toMail($user);

        if ($mailMessage instanceof MailMessage) {
            $verificationUrl = $mailMessage->actionUrl;

            return true;
        }

        return false;
    });

    get($verificationUrl)->assertRedirect(route('generate', [
        'serverSuccess' => "Your email is verified, thank you! I've also gave you some free credits to start.",
        'eventId'       => $icsEvent->id,
        'eventSecret'   => $icsEvent->secret,
    ], absolute: false));

    Event::assertDispatched(Verified::class);

    expect($user->refresh()->email_verified_at)->not()->toBeNull();

    Queue::assertPushed(GenerateCalendarJob::class);
});

test("guest can't generate icsEvent twice", function () {
    $this->assertGuest();

    $user = User::find(1);
    $this->assertNull($user);

    $email         = fake()->unique()->safeEmail();
    $calendarEvent = fake()->text(150);
    $timeZone      = fake()->timezone();

    post(route('guest-generate-calendar'),
        compact('email', 'calendarEvent', 'timeZone'))->assertStatus(200)
        ->assertJson(['reply' => 'ok']);

    post(route('guest-generate-calendar'),
        compact('email', 'calendarEvent', 'timeZone'))->assertStatus(401)
        ->assertJson(['error' => 'You need to verify your account to use Calendize!']);
});

test('user can generate icsEvents', function () {
    Event::fake();
    Notification::fake();
    Queue::fake();

    $user = User::factory()->create([
        'credits'           => 5,
        'email_verified_at' => Carbon::now(),
    ]);

    $calendarEvent = fake()->text(150);

    actingAs($user)
        ->post(route('generate-calendar', compact('calendarEvent')))
        ->assertOk();

    Queue::assertPushed(GenerateCalendarJob::class);
});

test("user with no credits can't generate icsEvents", function () {
    Queue::fake();

    $user = User::factory()->create([
        'credits'           => 0,
        'email_verified_at' => Carbon::now(),
    ]);

    $calendarEvent = fake()->text(150);

    actingAs($user)
        ->post(route('generate-calendar', compact('calendarEvent')))
        ->assertStatus(403)
        ->assertJson(['error' => 'You have no credits left!']);

    Queue::assertNotPushed(GenerateCalendarJob::class);
});

test("user with too many errors can't generate icsEvents", function () {
    Queue::fake();

    $user = User::factory()->create([
        'credits'           => 5,
        'failed_requests'   => 6,
        'email_verified_at' => Carbon::now(),
    ]);

    $calendarEvent = fake()->text(150);

    actingAs($user)
        ->post(route('generate-calendar', compact('calendarEvent')))
        ->assertStatus(403)
        ->assertJson(['error' => 'You have generated more errors than credits - this is suspected as spam. Please top-up your account to reset the error count.']);

    Queue::assertNotPushed(GenerateCalendarJob::class);
});

test('user can download events', function () {
    $icsEvent = IcsEvent::factory()->icsProcessed()->create();

    $response = get(route('event.download', ['id' => $icsEvent->id, 'secret' => $icsEvent->secret]));
    expect($response->getStatusCode())->toBe(200)
        ->and($response->headers->get('Content-Type'))->toEqual('text/calendar; charset=UTF-8')
        ->and($response->streamedContent())->toEqual($icsEvent->ics);

    $wrongIdResponse = get(route('event.download', ['id' => $icsEvent->id + 1, 'secret' => $icsEvent->secret]));
    expect($wrongIdResponse->getStatusCode())->toBe(404);

    $wrongSecretResponse = get(route('event.download', ['id' => $icsEvent->id, 'secret' => $icsEvent->secret . 'wrong']));
    expect($wrongSecretResponse->getStatusCode())->toBe(403)
        ->and($wrongSecretResponse->getContent())->toContain('The secret code is wrong!');
});

test('user can get their events list', function () {
    $user = User::factory()->create();
    IcsEvent::factory(9)->icsProcessed()->create(['user_id' => $user->id]);

    get(route('my-events'))->assertStatus(302);

    actingAs($user)->get(route('my-events'))->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('MyEvents')
            ->has('events.data', 9)
            ->where('events.data.8.id', 1)
            ->where('events.data.8.ics', IcsEvent::find(1)->ics)
            ->where('events.data.8.summary', IcsEvent::find(1)->getSummary())
            ->where('events.data.7.id', 2)
            ->where('events.data.7.ics', IcsEvent::find(2)->ics)
            ->where('events.data.7.summary', IcsEvent::find(2)->getSummary())
            ->where('events.data.6.id', 3)
            ->where('events.data.6.ics', IcsEvent::find(3)->ics)
            ->where('events.data.6.summary', IcsEvent::find(3)->getSummary())
            ->where('events.data.5.id', 4)
            ->where('events.data.5.ics', IcsEvent::find(4)->ics)
            ->where('events.data.5.summary', IcsEvent::find(4)->getSummary())
            ->where('events.data.4.id', 5)
            ->where('events.data.4.ics', IcsEvent::find(5)->ics)
            ->where('events.data.4.summary', IcsEvent::find(5)->getSummary())
            ->where('events.data.3.id', 6)
            ->where('events.data.3.ics', IcsEvent::find(6)->ics)
            ->where('events.data.3.summary', IcsEvent::find(6)->getSummary())
            ->where('events.data.2.id', 7)
            ->where('events.data.2.ics', IcsEvent::find(7)->ics)
            ->where('events.data.2.summary', IcsEvent::find(7)->getSummary())
            ->where('events.data.1.id', 8)
            ->where('events.data.1.ics', IcsEvent::find(8)->ics)
            ->where('events.data.1.summary', IcsEvent::find(8)->getSummary())
            ->where('events.data.0.id', 9)
            ->where('events.data.0.ics', IcsEvent::find(9)->ics)
            ->where('events.data.0.summary', IcsEvent::find(9)->getSummary())
        );
});
