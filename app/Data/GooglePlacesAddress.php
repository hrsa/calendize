<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class GooglePlacesAddress extends Data
{
    public function __construct(
        public ?string $address = null,
        public ?string $lat = null,
        public ?string $lng = null,
    ) {}
}
