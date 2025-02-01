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
        public ?IncomingTelegramMessageLocation $location = null,
    ) {}

    public function hasCommand(): bool
    {
        return $this->command instanceof \App\Enums\TelegramCommand;
    }

    public function hasText(): bool
    {
        return $this->text !== null;
    }

    public function hasCallbackData(): bool
    {
        return $this->data !== null;
    }

    public function hasLocation(): bool
    {
        return $this->location !== null;
    }
}
