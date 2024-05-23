<?php

namespace App\Data\Telegram;

use App\Enums\TelegramCommand;
use Spatie\LaravelData\Data;

class IncomingTelegramMessage extends Data
{
    public function __construct(
        public IncomingTelegramMessageAuthor $author,
        public ?string $text,
        public ?string $data,
        public ?TelegramCommand $command = null,
    ) {
    }
}
