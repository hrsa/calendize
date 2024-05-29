<?php

namespace App\Notifications\Telegram\User;

use App\Enums\TelegramCallback;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use JsonException;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class MyEvents extends Notification
{
    public function __construct(protected LengthAwarePaginator $events)
    {
    }

    /**
     * @return array<string>
     */
    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram(User $notifiable): TelegramBase|TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to($notifiable->telegram_id);

        if ($this->events->isEmpty()) {
            $telegramMessage->content("You haven't calendized anything yet!");
            try {
                $telegramMessage->button("It's never too late to try!", route('generate'));
                $telegramMessage->button('Quickstart', route('how-to-use'));
            } catch (JsonException $e) {
                Log::error($e->getMessage(), [$e]);
            }
        } else {
            $telegramMessage->content("Here are your events: ({$this->events->currentPage()}/{$this->events->lastPage()})");
            foreach ($this->events as $event) {
                try {
                    $telegramMessage->buttonWithCallback(
                        text: $event->getSummary(),
                        callback_data: TelegramCallback::GetEvent->value . '=' . $event->id);
                } catch (JsonException $e) {
                    Log::error($e->getMessage(), [$e]);
                }
            }
        }

        if ($this->events->hasMorePages()) {
            try {
                $telegramMessage->buttonWithCallback(
                    text: 'Load more',
                    callback_data: TelegramCallback::GetEventsOnPage->value . '=' . ($this->events->currentPage() + 1));
            } catch (JsonException $e) {
                Log::error($e->getMessage(), [$e]);
            }
        }

        return $telegramMessage;
    }
}
