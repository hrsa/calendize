<?php

namespace App\Notifications\Telegram\User;

use App\Models\IcsEvent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use NotificationChannels\Telegram\TelegramMessage;

class IcsEventError extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IcsEvent $icsEvent) {}

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram(User $notifiable): TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content('Sorry, there was an error 😟')
            ->line('')
            ->line('')
            ->line("Here is the reason why I couldn't calendize your event:")
            ->line($this->icsEvent->error);

        if (Gate::forUser($notifiable)->denies('errors-under-threshold')) {
            $telegramMessage->line('')
                ->line('Because you have more errors than credits,
                    your balance is going down with every new error.')
                ->line("You have {$notifiable->credits} left.");
        }

        return $telegramMessage;
    }
}
