<?php

namespace App\Notifications\Telegram\User;

use App\Models\IcsEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Telegram\TelegramFile;

class IcsEventValid extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IcsEvent $icsEvent, public ?string $message = null)
    {
    }

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramFile
    {
        $textData = $this->message ? "{$this->message} \n ***{$this->icsEvent->getSummary()}***" : $this->icsEvent->getSummary();
        $textData .= "\n\nYou have {$notifiable->credits} credits left!";

        $filename = storage_path(Str::slug($this->icsEvent->getSummary(), '_') . '.ics');
        $data = $this->icsEvent->ics;
        file_put_contents($filename, $data);

        $telegramMessage = TelegramFile::create()
            ->to($notifiable->telegram_id)
            ->content($textData)
            ->document($filename, Str::slug($this->icsEvent->getSummary(), '_') . '.ics');

        unlink($filename);

        return $telegramMessage;
    }
}
