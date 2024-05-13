<?php

namespace App\Notifications\Telegram\Admin;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Notifications\BaseNotification;

class BackupWasSuccessfulNotification extends BaseNotification
{
    public function __construct(public BackupWasSuccessful $event)
    {
    }

    public function toTelegram($notifiable): \NotificationChannels\Telegram\TelegramBase|TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to(config('backup.notifications.telegram.chat_id'))
            ->content(trans('backup::notifications.backup_successful_subject_title'));

        $this->backupDestinationProperties()->each(function ($value, $name) use ($telegramMessage) {
            $telegramMessage->line("{$name}: {$value}");
        });

        return $telegramMessage;
    }
}
