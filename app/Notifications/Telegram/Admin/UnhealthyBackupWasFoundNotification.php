<?php

namespace App\Notifications\Telegram\Admin;

use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\UnhealthyBackupWasFound;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification as SpatieUnhealthyBackupWasFoundNotification;

class UnhealthyBackupWasFoundNotification extends SpatieUnhealthyBackupWasFoundNotification
{
    public function __construct(public UnhealthyBackupWasFound $event)
    {
        parent::__construct($event);
    }

    public function toTelegram($notifiable): TelegramBase|TelegramMessage
    {
        $telegramMessage = TelegramMessage::create()
            ->to(config('backup.notifications.telegram.chat_id'))
            ->content(trans('backup::notifications.unhealthy_backup_found_subject', ['application_name' => $this->applicationName()]))
            ->line(trans('backup::notifications.unhealthy_backup_found_body', ['application_name' => $this->applicationName(), 'disk_name' => $this->diskName()]))
            ->line($this->problemDescription());

        $this->backupDestinationProperties()->each(function ($value, $name) use ($telegramMessage) {
            $telegramMessage->line("{$name}: {$value}");
        });

        if ($this->failure()->wasUnexpected()) {
            $telegramMessage
                ->line('Health check: ' . $this->failure()->healthCheck()->name())
                ->line(trans('backup::notifications.exception_message', ['message' => $this->failure()->exception()->getMessage()]))
                ->line(trans('backup::notifications.exception_trace', ['trace' => $this->failure()->exception()->getTraceAsString()]));
        }

        return $telegramMessage;
    }
}
