<?php

namespace App\Notifications\Telegram\User;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Enums\TelegramCallback;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use JsonException;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class ReplyToUnknownCommand extends Notification
{
    public function __construct(public IncomingTelegramMessage $incomingMessage) {}

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram(User $notifiable): TelegramBase|TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content('I got your message!')
            ->line('')
            ->line('')
            ->line("But i don't really know what to do with it...")
            ->line('Do you want me to **calendize** it?');

        if ($notifiable->timezone) {
            $telegramMessage
                ->line('')
                ->line('')
                ->line("Your timezone is {$notifiable->timezone}. If you want to change it - send me your location **before** clicking the button below.");
        } else {
            $telegramMessage
                ->line('')
                ->line('')
                ->line('**I need to know your timezone to respect your calendar.** Please send me your location **before** clicking the button below!');
        }

        try {
            $telegramMessage->buttonWithCallback(
                text: 'Yes, calendize my message!',
                callbackData: TelegramCallback::Calendize->value . '=' . $this->incomingMessage->author->id);
        } catch (JsonException $jsonException) {
            Log::error($jsonException->getMessage(), [$jsonException]);
        }

        return $telegramMessage;
    }
}
