<?php

namespace App\Data\IcsEvent;

use Spatie\LaravelData\Data;

/**
 * @property IcsEventData[] $events
 */
class IcsEventsArray extends Data
{
    public function __construct(
        public array $events,
    ) {
    }
}
