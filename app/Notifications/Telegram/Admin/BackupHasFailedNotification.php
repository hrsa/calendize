<?php

namespace App\Notifications\Telegram\Admin;

use Illuminate\Support\Facades\Config;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Notifications\BaseNotification;

class BackupHasFailedNotification extends BaseNotification
{
    public function __construct(public BackupHasFailed $event)
    {
    }

    public function toTelegram($notifiable): TelegramBase|TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to(Config::string('backup.notifications.telegram.chat_id'))
            ->content(trans('backup::notifications.backup_failed_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.backup_failed_body', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.exception_message', ['message' => $this->event->exception->getMessage()]))
            ->line(trans('backup::notifications.exception_trace', ['trace' => $this->event->exception->getTraceAsString()]));

        $this->backupDestinationProperties()->each(function ($value, $name) use ($telegramMessage) {
            $telegramMessage->line("{$name}: {$value}");
        });

        return $telegramMessage;
    }
}
