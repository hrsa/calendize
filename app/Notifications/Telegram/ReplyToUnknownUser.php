<?php

namespace App\Notifications\Telegram;

use App\Data\Telegram\IncomingTelegramMessageAuthor;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class ReplyToUnknownUser extends Notification
{
    public function __construct(public IncomingTelegramMessageAuthor $author)
    {
    }

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramBase|TelegramMessage
    {
        return TelegramMessage::create()
            ->content("Hmm... I'm not sure if you are already a Calendize user, {$this->author->firstName}...")
            ->line('')
            ->line('')
            ->line("If you are - let's authorize you!")
            ->line('')
            ->line("Don't forget to login ***BEFORE*** you click the \"Connect\" button!")
            ->button('1️⃣ Login', route('login'))
            ->button('2️⃣ Connect my account', route('telegram.connect', ['tgid' => base64_encode($this->author->id)]));
    }
}
