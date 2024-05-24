<?php

namespace App\Data\IcsEvent;

use Spatie\LaravelData\Data;

class RecurrenceRule extends Data
{
    public function __construct(
        public ?string $frequency = null,
        public ?string $times = null,
        public ?string $interval = null,
        public ?string $starting = null,
        public ?string $until = null,
    ) {
    }
}
