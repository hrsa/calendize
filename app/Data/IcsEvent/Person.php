<?php

namespace App\Data\IcsEvent;

use Spatie\LaravelData\Data;

class Person extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
    ) {}
}
