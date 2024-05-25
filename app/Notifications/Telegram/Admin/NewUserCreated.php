<?php

namespace App\Notifications\Telegram\Admin;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Models\User;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class NewUserCreated extends Notification
{
    public function __construct(public User $user)
    {
    }

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramBase|TelegramMessage
    {
        return TelegramMessage::create()
            ->to(config('app.admin.telegram_chat_id'))
            ->content('🎉 A new user just signed up! 🎉')
            ->line('')
            ->line(":{$this->user->name} ({$this->user->email})");
    }
}
