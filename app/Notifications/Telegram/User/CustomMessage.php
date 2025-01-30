<?php

namespace App\Notifications\Telegram\User;

use App\Models\User;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class CustomMessage extends Notification
{
    public function __construct(public $message) {}

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram(User $notifiable): TelegramBase|TelegramMessage
    {
        return TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content($this->message);
    }
}
