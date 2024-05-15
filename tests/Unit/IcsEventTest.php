<?php

use App\Exceptions\NoSummaryException;
use App\Models\IcsEvent;

test('valid IcsEvent has ics and summary, no errors', function () {
    $ics = IcsEvent::factory()->icsProcessed()->create();

    expect($ics->is_successful())->toBeTrue()
        ->and($ics->secret)->toBeString()
        ->and($ics->ics)->toBeString()
        ->and($ics->error)->toBeNull()
        ->and($ics->getSummary())->toBeString();
});

test('IcsEvent with an error has no ics and throws NoSummaryException', function () {
    $ics = IcsEvent::factory()->icsError()->create();

    expect($ics->is_successful())->toBeFalse()
        ->and($ics->secret)->toBeString()
        ->and($ics->error)->toBeString()
        ->and($ics->ics)->toBeNull()
        ->and($ics->getSummary())->toThrow(NoSummaryException::class);
})->throws(NoSummaryException::class);
