<?php

use App\Events\IcsEventProcessed;
use App\Jobs\GenerateCalendarJob;
use App\Models\IcsEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

test('ICS event is updated with successful OpenAI response', function () {
    $ics          = IcsEvent::factory()->icsProcessed()->create();
    $validIcsData = str_replace("\n", '\\n', $ics->ics);
    $ics->update(['ics' => null]);
    $ics->refresh();

    expect($ics->ics)->toBeNull()
        ->and($validIcsData)->toBeString();

    Event::fake();
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'role'    => 'assistant',
                        'content' => '{"ics": "' . $validIcsData . '"}',
                    ],
                ],
            ],
        ]),
    ]);

    GenerateCalendarJob::dispatch($ics);
    Event::assertDispatched(IcsEventProcessed::class);

    $ics->refresh();
    expect($ics->ics)->not()->toBeNull()
        ->and($ics->error)->toBeNull()
        ->and($ics->getSummary())->toBeString();
});

test('ICS event is updated with error from OpenAI response', function () {
    $ics = IcsEvent::factory()->create();

    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toBeNull();

    Event::fake();
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
    Event::assertDispatched(IcsEventProcessed::class);

    $ics->refresh();
    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toEqual('Sorry an error is faked here!');
});

test('ICS event is updated with data from Mistral in case OpenAI fails', function () {
    $ics          = IcsEvent::factory()->icsProcessed()->create();
    $validIcsData = str_replace("\n", '\\n', $ics->ics);
    $ics->update(['ics' => null]);
    $ics->refresh();

    expect($ics->ics)->toBeNull()
        ->and($validIcsData)->toBeString();

    Event::fake();
    OpenAI::fake([
    ]);

    Http::fake([
        'api.mistral.ai/*' => Http::response([
            'choices' => [0 => [
                'index'   => 0,
                'message' => [
                    'role'    => 'assistant',
                    'content' => '{"ics": "' . $validIcsData . '"}',
                ],
                'finish_reason' => 'stop',
            ], ],
            'usage' => [
                'prompt_tokens'     => 14,
                'total_tokens'      => 29,
                'completion_tokens' => 15,
            ],
        ]),
    ]);

    GenerateCalendarJob::dispatch($ics);
    Event::assertDispatched(IcsEventProcessed::class);

    $ics->refresh();
    expect($ics->ics)->not()->toBeNull()
        ->and($ics->error)->toBeNull()
        ->and($ics->getSummary())->toBeString();
});

test('ICS event is updated with error from Mistral response after OpenAI fails', function () {
    $ics = IcsEvent::factory()->create();

    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toBeNull();

    Event::fake();
    OpenAI::fake([]);
    Http::fake([
        'api.mistral.ai/*' => Http::response([
            'choices' => [0 => [
                'index'   => 0,
                'message' => [
                    'role'    => 'assistant',
                    'content' => '{"error": "Sorry an error is faked here!"}',
                ],
                'finish_reason' => 'stop',
            ], ],
            'usage' => [
                'prompt_tokens'     => 14,
                'total_tokens'      => 29,
                'completion_tokens' => 15,
            ],
        ]),
    ]);

    GenerateCalendarJob::dispatch($ics);
    Event::assertDispatched(IcsEventProcessed::class);

    $ics->refresh();
    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toEqual('Sorry an error is faked here!');
});

test('ICS event is updated with error if both Mistral and OpenAI fail', function () {
    $ics = IcsEvent::factory()->create();

    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toBeNull();

    Event::fake();
    OpenAI::fake([]);
    Http::fake([
        'api.mistral.ai/*' => Http::response("We're down, don't bother!", Symfony\Component\HttpFoundation\Response::HTTP_SERVICE_UNAVAILABLE),
    ]);

    GenerateCalendarJob::dispatch($ics);

    Event::assertDispatched(IcsEventProcessed::class);

    $ics->refresh();

    expect($ics->ics)->toBeNull()
        ->and($ics->error)->toEqual("I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!");
});
