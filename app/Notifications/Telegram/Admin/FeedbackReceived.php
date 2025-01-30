<?php

namespace App\Notifications\Telegram\Admin;

use App\Exceptions\NoSummaryException;
use App\Models\Feedback;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class FeedbackReceived extends Notification
{
    private string $title;

    public function __construct(public Feedback $feedback)
    {
        $this->title = "New feedback from {$this->feedback->user->email}! ";
        $this->feedback->like ? $this->title .= '👍' : $this->title .= '👎';
    }

    public function via(): array
    {
        return ['telegram'];
    }

    /**
     * @throws NoSummaryException
     */
    public function toTelegram($notifiable): TelegramBase|TelegramMessage
    {
        return TelegramMessage::create()
            ->to(Config::string('app.admin.telegram_chat_id'))
            ->content("{$this->title}")
            ->line(' ')
            ->line(' ')
            ->line("{$this->feedback->icsEvent->getSummary()} (id: {$this->feedback->ics_event_id})")
            ->line(' ')
            ->line("{$this->feedback->data}");
    }
}
