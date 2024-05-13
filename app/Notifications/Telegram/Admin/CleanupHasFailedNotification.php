<?php

namespace App\Notifications\Telegram\Admin;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\CleanupHasFailed;
use Spatie\Backup\Notifications\BaseNotification;

class CleanupHasFailedNotification extends BaseNotification
{
    public function __construct(public CleanupHasFailed $event)
    {
    }

    public function toTelegram($notifiable): \NotificationChannels\Telegram\TelegramBase|TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to(config('backup.notifications.telegram.chat_id'))
            ->content(trans('backup::notifications.cleanup_failed_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.cleanup_failed_body', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.exception_message', ['message' => $this->event->exception->getMessage()]))
            ->line(trans('backup::notifications.exception_trace', ['trace' => $this->event->exception->getTraceAsString()]));

        $this->backupDestinationProperties()->each(function ($value, $name) use ($telegramMessage) {
            $telegramMessage->line("{$name}: {$value}");
        });

        return $telegramMessage;
    }
}
