<?php

namespace App\Notifications\Telegram\User;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class CustomMesssage extends Notification
{
    public function __construct(public $message)
    {
    }

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramBase|TelegramMessage
    {
        return TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content($this->message);
    }
}
