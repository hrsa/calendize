<?php

namespace App\Services;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Enums\TelegramCallback;
use App\Models\User;
use App\Notifications\Telegram\Admin\UnknownMessageReceived;
use App\Notifications\Telegram\ReplyToUnknownCommand;
use App\Notifications\Telegram\ReplyToUnknownUser;
use App\Notifications\Telegram\User\CreditsRemaining;
use App\Notifications\Telegram\User\CustomMesssage;
use App\Notifications\Telegram\User\IcsEventValid;
use App\Notifications\Telegram\User\MyEvents;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class TelegramService
{
    public function process(IncomingTelegramMessage $telegramMessage): void
    {
        $user = User::whereTelegramId($telegramMessage->author->id)->first();

        if (!$user) {
            Notification::send('', new ReplyToUnknownUser($telegramMessage->author));
            Notification::send('', new UnknownMessageReceived($telegramMessage));

            return;
        }

        if (!$telegramMessage->command && $telegramMessage->text) {
            $this->forwardToAdminAndReply($telegramMessage);
        }

        if ($telegramMessage->data) {
            $this->processCallbackQuery($telegramMessage, $user);
        }

        $method = 'handle' . ($telegramMessage->command?->name);
        if (method_exists($this, $method)) {
            $this->$method($user);
        }
    }

    public function forwardToAdminAndReply(IncomingTelegramMessage $telegramMessage): void
    {
        Notification::send('', new UnknownMessageReceived($telegramMessage));
        Notification::send('', new ReplyToUnknownCommand($telegramMessage));
    }

    private function processCallbackQuery(IncomingTelegramMessage $telegramMessage, User $user): void
    {
        foreach (TelegramCallback::cases() as $callback) {
            if (Str::startsWith($telegramMessage->data, $callback->value)) {
                $parameter = Str::of($telegramMessage->data)->replaceStart($callback->value . '=', '')->value();

                switch ($callback) {
                    case TelegramCallback::GetEventsOnPage:
                        $this->handleMyEvents($user, (int) $parameter);
                        break;
                    case TelegramCallback::GetEvent:
                        $this->handleGetEvent($user, (int) $parameter);
                        break;
                }
                break;
            }
        }
    }

    private function handleNotifyMe(User $user): void
    {
        $user->update(['send_tg_notifications' => true]);
        $user->notify(new CustomMesssage("Great! Now I'll send you calendized events via Telegram! 😎"));
    }

    private function handleDontNotifyMe(User $user): void
    {
        $user->update(['send_tg_notifications' => false]);
        $user->notify(new CustomMesssage('Alright... no more notifications about calendized events!'));
    }

    private function handleMyEvents(User $user, int $page = 1): void
    {
        $events = $user->processedIcsEvents()->paginate(6, page: $page);
        $user->notify(new MyEvents($events));
    }

    private function handleMyCredits(User $user): void
    {
        $user->notify(new CreditsRemaining());
    }

    private function handleGetEvent(User $user, int $eventId): void
    {
        $event = $user->icsEvents->find($eventId);

        if (!$event) {
            $user->notify(new CustomMesssage("That event doesn't exist! Have you deleted it?"));

            return;
        }

        if (!$event->is_successful()) {
            $user->notify(new CustomMesssage("I couldn't calendize this event, because of this error: \n\n**{$event->error}**\n\nSo I can't send you the file 😥"));

            return;
        }

        $user->notify(new IcsEventValid(icsEvent: $event, message: 'Here is your event!'));
    }
}
