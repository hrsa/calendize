<?php

namespace App\Data\Telegram;

use Spatie\LaravelData\Data;

class IncomingTelegramMessageLocation extends Data
{
    public function __construct(
        public string $latitude,
        public string $longitude,
    ) {}
}
