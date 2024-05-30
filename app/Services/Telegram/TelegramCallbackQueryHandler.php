<?php

namespace App\Services\Telegram;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Enums\TelegramCallback;
use App\Models\IcsEvent;
use App\Models\User;
use App\Notifications\Telegram\User\CustomMessage;
use App\Notifications\Telegram\User\IcsEventValid;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class TelegramCallbackQueryHandler
{
    private TelegramCommandHandler $commandHandler;

    public function __construct(public User $user, public IncomingTelegramMessage $telegramMessage)
    {
        $this->commandHandler = new TelegramCommandHandler($this->user, $this->telegramMessage);
    }

    public function handleCallbackQuery(): void
    {
        foreach (TelegramCallback::cases() as $callback) {
            if (Str::startsWith($this->telegramMessage->data, $callback->value)) {
                $parameter = Str::of($this->telegramMessage->data)->replaceStart($callback->value . '=', '')->value();

                switch ($callback) {
                    case TelegramCallback::GetEventsOnPage:
                        $this->commandHandler->handleMyEvents($this->user, (int) $parameter);
                        break;
                    case TelegramCallback::GetEvent:
                        $this->handleGetEvent($this->user, (int) $parameter);
                        break;
                    case TelegramCallback::Calendize:
                        $this->handleCalendizeCallback($this->user, (int) $parameter);
                        break;
                }
                break;
            }
        }
    }

    private function handleCalendizeCallback(User $user, $redisKey): void
    {
        $prompt = Redis::get($redisKey);

        if (!$prompt) {
            $user->notify(new CustomMessage("Sorry, i have forgot about that data...\n I only remember what you told me less than 1 hour ago 😊 \nCould you please re-send it to me?"));

            return;
        }

        $this->commandHandler->handleCalendize($user, $prompt);
    }

    private function handleGetEvent(User $user, int $eventId): void
    {
        /** @var IcsEvent|null $event */
        $event = $user->icsEvents->find($eventId);

        if ($event === null) {
            $user->notify(new CustomMessage("That event doesn't exist! Have you deleted it?"));

            return;
        }

        if (!$event->is_successful()) {
            $user->notify(new CustomMessage("I couldn't calendize this event, because of this error: \n\n**{$event->error}**\n\nSo I can't send you the file 😥"));

            return;
        }

        $user->notify(new IcsEventValid(icsEvent: $event, message: 'Here is your event!'));
    }
}
