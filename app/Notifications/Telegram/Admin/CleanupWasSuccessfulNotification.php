<?php

namespace App\Notifications\Telegram\Admin;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Events\CleanupWasSuccessful;
use Spatie\Backup\Notifications\BaseNotification;

class CleanupWasSuccessfulNotification extends BaseNotification
{
    public function __construct(public CleanupWasSuccessful $event)
    {
    }

    public function toTelegram($notifiable): \NotificationChannels\Telegram\TelegramBase|TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to(config('backup.notifications.telegram.chat_id'))
            ->content(trans('backup::notifications.cleanup_successful_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.cleanup_successful_body', ['application_name' => $this->applicationName(), 'disk_name' => $this->diskName()]));

        $this->backupDestinationProperties()->each(function ($value, $name) use ($telegramMessage) {
            $telegramMessage->line("{$name}: {$value}");
        });

        return $telegramMessage;
    }
}
