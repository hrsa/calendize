<?php

use App\Events\IcsEventProcessed;
use App\Jobs\GenerateCalendarJob;
use App\Mail\IcsError;
use App\Mail\IcsValid;
use App\Models\IcsEvent;
use App\Models\User;
use App\Notifications\Telegram\User\IcsEventError;
use App\Notifications\Telegram\User\IcsEventValid;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

beforeEach(function () {
    Event::fake();
    Mail::fake();
    Notification::fake();

    $this->validResponseData = '{
    "starts": {
            "at": "2024-05-24T12:00:00",
            "timezone": "Europe/Lisbon"
        }
    ,
    "ends": {
            "at": "2024-05-24T12:30:00",
            "timezone": "Europe/Lisbon"
        }
    ,
    "name": "A fine title",
    "description": "A nice description",
    "address": "Sesame street, 44",
    "addressName": "",
    "url": "https://google.com",
    "googleConference": "meet.google.com/que-cejk-foj",
    "microsoftTeams": "",
    "organizer": {
        "name": "Jack Black",
        "email": "jack@black.com"
    },
    "attendees": [
        {
            "name": "Tester of Tests",
            "email": "test@test.com"
        }
    ],
    "isFullDay": false,
    "rrule": []
}';

});

test('ICS event is updated with successful OpenAI response', function () {
    $ics = IcsEvent::factory()->create();

    expect($ics->ics)->toBeNull();

    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'role'    => 'assistant',
                        'content' => $this->validResponseData,
                    ],
                ],
            ],
        ]),
    ]);

    GenerateCalendarJob::dispatch($ics);
    Event::assertDispatched(IcsEventProcessed::class);
    Mail::assertSent(IcsValid::class);
    Mail::assertNotSent(IcsError::class);
    Notification::assertNothingSent();

    $ics->refresh();

    expect($ics->ics)->not()->toBeNull()
        ->and($ics->error)->toBeNull()
        ->and($ics->getSummary())->toBeString();
});

test('ICS event is updated with error from OpenAI response', function () {
    $ics = IcsEvent::factory()->create();

    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toBeNull();

    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'role'    => 'assistant',
                        'content' => '{"error": "Sorry an error is faked here!"}',
                    ],
                ],
            ],
        ]),
    ]);

    GenerateCalendarJob::dispatch($ics);
    Mail::assertNotSent(IcsValid::class);
    Mail::assertSent(IcsError::class);
    Event::assertDispatched(IcsEventProcessed::class);
    Notification::assertNothingSent();

    $ics->refresh();
    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toEqual('Sorry an error is faked here!');
});

test('ICS event is updated with data from Mistral in case OpenAI fails', function () {
    $ics = IcsEvent::factory()->create();

    expect($ics->ics)->toBeNull();

    OpenAI::fake([
    ]);

    Http::fake([
        'api.mistral.ai/*' => Http::response([
            'choices' => [
                0 => [
                    'index'   => 0,
                    'message' => [
                        'role'    => 'assistant',
                        'content' => $this->validResponseData,
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens'     => 14,
                'total_tokens'      => 29,
                'completion_tokens' => 15,
            ],
        ]),
    ]);

    GenerateCalendarJob::dispatch($ics);
    Event::assertDispatched(IcsEventProcessed::class);
    Mail::assertSent(IcsValid::class);
    Mail::assertNotSent(IcsError::class);
    Notification::assertNothingSent();

    $ics->refresh();
    expect($ics->ics)->not()->toBeNull()
        ->and($ics->error)->toBeNull()
        ->and($ics->getSummary())->toBeString();
});

test('ICS event is updated with error from Mistral response after OpenAI fails', function () {
    $ics = IcsEvent::factory()->create();

    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toBeNull();

    OpenAI::fake([]);
    Http::fake([
        'api.mistral.ai/*' => Http::response([
            'choices' => [
                0 => [
                    'index'   => 0,
                    'message' => [
                        'role'    => 'assistant',
                        'content' => '{"error": "Sorry an error is faked here!"}',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
            'usage' => [
                'prompt_tokens'     => 14,
                'total_tokens'      => 29,
                'completion_tokens' => 15,
            ],
        ]),
    ]);

    GenerateCalendarJob::dispatch($ics);
    Event::assertDispatched(IcsEventProcessed::class);
    Mail::assertNotSent(IcsValid::class);
    Mail::assertSent(IcsError::class);
    Notification::assertNothingSent();

    $ics->refresh();
    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toEqual('Sorry an error is faked here!');
});

test('ICS event is updated with error if both Mistral and OpenAI fail', function () {
    $ics = IcsEvent::factory()->create();

    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toBeNull();

    OpenAI::fake([]);
    Http::fake([
        'api.mistral.ai/*' => Http::response("We're down, don't bother!", Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE),
    ]);

    GenerateCalendarJob::dispatch($ics);

    Event::assertDispatched(IcsEventProcessed::class);
    Mail::assertSent(IcsError::class);
    Mail::assertNotSent(IcsValid::class);
    Notification::assertNothingSent();


    $ics->refresh();
    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toEqual("I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!");
});

test('Calendized event triggers user Telegram notification', function () {
    $user = User::factory()->create(['telegram_id' => 123456, 'send_tg_notifications' => true]);
    $ics = IcsEvent::factory()->create(['user_id' => $user->id]);

    expect($ics->ics)->toBeNull();

    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'role'    => 'assistant',
                        'content' => $this->validResponseData,
                    ],
                ],
            ],
        ]),
    ]);

    GenerateCalendarJob::dispatch($ics);
    Event::assertDispatched(IcsEventProcessed::class);
    Notification::assertSentTo($user, IcsEventValid::class);
    Notification::assertNotSentTo($user, IcsEventError::class);
    Mail::assertSent(IcsValid::class);
    Mail::assertNotSent(IcsError::class);

    $ics->refresh();

    expect($ics->ics)->not()->toBeNull()
        ->and($ics->error)->toBeNull()
        ->and($ics->getSummary())->toBeString();
});

test('Failed event triggers user Telegram notification', function () {
    $user = User::factory()->create(['telegram_id' => 123456, 'send_tg_notifications' => true]);
    $ics = IcsEvent::factory()->create(['user_id' => $user->id]);

    expect($ics->ics)->toBeNull();

    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toBeNull();

    OpenAI::fake([]);
    Http::fake([
        'api.mistral.ai/*' => Http::response("We're down, don't bother!", Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE),
    ]);

    GenerateCalendarJob::dispatch($ics);
    Event::assertDispatched(IcsEventProcessed::class);
    Notification::assertNotSentTo($user, IcsEventValid::class);
    Notification::assertSentTo($user, IcsEventError::class);
    Mail::assertNotSent(IcsValid::class);
    Mail::assertSent(IcsError::class);

    $ics->refresh();

    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toEqual("I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!");
});
