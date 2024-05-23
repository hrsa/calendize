<?php

namespace App\Notifications\Telegram;

use App\Data\Telegram\IncomingTelegramMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class ReplyToUnknownCommand extends Notification
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
        return TelegramMessage::create()
            ->to($this->incomingMessage->author->id)
            ->content('I got your message!')
            ->line('')
            ->line('')
            ->line("But i don't really know what to do with it...")
            ->line('Are you sure you have used the right command?')
            ->line('')
            ->line("To be sure we don't miss anything, I've forwarded your message to my creator. He'll be able to help!");
    }
}
