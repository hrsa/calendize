<?php

namespace App\Notifications\Telegram\Admin;

use App\Data\Telegram\IncomingTelegramMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class UnknownMessageReceived extends Notification
{
    public function __construct(public IncomingTelegramMessage $incomingMessage)
    {
    }

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramBase|TelegramMessage
    {
        $author = $this->incomingMessage->author;

        $telegramMessage = TelegramMessage::create()
            ->content('Unknown command!')
            ->line('')
            ->line("From: {$author->firstName} {$author->lastName} (@{$author->userName})")
            ->line("Message: \"{$this->incomingMessage->text}\"");

        if ($author->isBot) {
            $telegramMessage->line("It's a bot, by the way...");
        }

        return $telegramMessage;
    }
}
