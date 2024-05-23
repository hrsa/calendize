<?php

namespace App\Data\Telegram;

use Spatie\LaravelData\Data;

class IncomingTelegramMessageAuthor extends Data
{
    public function __construct(
        public string $id,
        public bool $isBot,
        public ?bool $isPremium,
        public string $firstName,
        public ?string $lastName,
        public string $userName,
        public ?string $languageCode,
    ) {
        $this->isPremium ??= false;
    }
}
