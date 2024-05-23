<?php

namespace App\Notifications\Telegram\User;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use JsonException;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class CreditsRemaining extends Notification
{
    public function __construct()
    {
    }

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram(User $notifiable): TelegramBase|TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content('You have ');

        if ($notifiable->credits > 1) {
            $telegramMessage->line("{$notifiable->credits} credits left.");
        }

        if ($notifiable->credits === 1) {
            $telegramMessage->line('1 credit left.');
        }

        if ($notifiable->credits === 0) {
            $telegramMessage->line('no more credits left!');
        }

        if ($notifiable->credits < 3) {
            try {
                $telegramMessage->button('Get more credits', route('dashboard'));
            } catch (JsonException $e) {
                Log::error($e->getMessage(), [$e]);
            }
        }

        return $telegramMessage;
    }
}
