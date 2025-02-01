<?php

namespace App\Data\IcsEvent;

use Spatie\LaravelData\Data;

class Date extends Data
{
    public function __construct(
        public string $at,
        public ?string $timezone,
    ) {}
}
