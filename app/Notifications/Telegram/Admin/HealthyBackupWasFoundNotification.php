<?php

namespace App\Notifications\Telegram\Admin;

use Illuminate\Support\Facades\Config;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\HealthyBackupWasFound;
use Spatie\Backup\Notifications\BaseNotification;

class HealthyBackupWasFoundNotification extends BaseNotification
{
    public function __construct(public HealthyBackupWasFound $event)
    {
    }

    public function toTelegram($notifiable): TelegramBase|TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to(Config::string('backup.notifications.telegram.chat_id'))
            ->content(trans('backup::notifications.healthy_backup_found_subject', ['application_name' => $this->applicationName(), 'disk_name' => $this->diskName()]))
            ->line(trans('backup::notifications.healthy_backup_found_body', ['application_name' => $this->applicationName()]));

        $this->backupDestinationProperties()->each(function ($value, $name) use ($telegramMessage) {
            $telegramMessage->line("{$name}: {$value}");
        });

        return $telegramMessage;
    }
}
