<?php

namespace App\Notifications\Telegram\Admin;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class NewUserCreated extends Notification
{
    public function __construct(public User $user) {}

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramBase|TelegramMessage
    {
        return TelegramMessage::create()
            ->to(Config::string('app.admin.telegram_chat_id'))
            ->content('🎉 A new user just signed up! 🎉')
            ->line('')
            ->line("{$this->user->name} ({$this->user->email})");
    }
}
