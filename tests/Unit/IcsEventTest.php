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

test('valid IcsEvent with multiple events has both events in the summary', function () {
    $ics = IcsEvent::factory()->icsProcessed()->create();

    $firstTitle = fake()->words(3, true);
    $secondTitle = fake()->words(1, true);
    $thirdTitle = fake()->words(7, true);

    $ics->ics = "BEGIN:VCALENDAR\n" .
                "VERSION:2.0\n" .
                "PRODID:-//Calendar//Calendize 2.0//EN\n" .
                "BEGIN:VEVENT\n" .
                "SUMMARY: {$firstTitle}\n" .
                "END:VEVENT\n" .
                "BEGIN:VEVENT\n" .
                "SUMMARY: {$secondTitle}\n" .
                "END:VEVENT\n" .
                "BEGIN:VEVENT\n" .
                "SUMMARY: {$thirdTitle}\n" .
                "END:VEVENT\n" .
                'END:VCALENDAR';

    expect($ics->is_successful())->toBeTrue()
        ->and($ics->secret)->toBeString()
        ->and($ics->ics)->toBeString()
        ->and($ics->error)->toBeNull()
        ->and($ics->getSummary())->toEqual("{$firstTitle}, {$secondTitle}, {$thirdTitle}");
});
