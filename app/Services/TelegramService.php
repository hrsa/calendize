<?php

namespace App\Services;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Enums\TelegramCallback;
use App\Enums\TelegramCommand;
use App\Models\IcsEvent;
use App\Models\User;
use App\Notifications\Telegram\Admin\UnknownMessageReceived;
use App\Notifications\Telegram\ReplyToUnknownCommand;
use App\Notifications\Telegram\ReplyToUnknownUser;
use App\Notifications\Telegram\User\CreditsRemaining;
use App\Notifications\Telegram\User\CustomMesssage;
use App\Notifications\Telegram\User\IcsEventValid;
use App\Notifications\Telegram\User\MyEvents;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class TelegramService
{
    public function process(IncomingTelegramMessage $telegramMessage): void
    {
        $this->findCommand($telegramMessage);

        $user = User::with('icsEvents')->whereTelegramId($telegramMessage->author->id)->first();

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

        if ($telegramMessage->command === TelegramCommand::Calendize) {
            $this->handleCalendize($user, $telegramMessage->text);
        } else {
            $method = 'handle' . ($telegramMessage->command?->name);
            if (method_exists($this, $method)) {
                $this->$method($user);
            }
        }
    }

    private function findCommand(IncomingTelegramMessage $telegramMessage): void
    {
        foreach (TelegramCommand::cases() as $command) {
            if (Str::startsWith($telegramMessage->text, $command->value)) {
                if (Str::length($telegramMessage->text) === Str::length($command->value)) {
                    $telegramMessage->text = '';
                } else {
                    $telegramMessage->text = Str::of($telegramMessage->text)->replaceStart($command->value . ' ', '')->value();
                }
                $telegramMessage->command = $command;
                break;
            }
        }
    }

    public function forwardToAdminAndReply(IncomingTelegramMessage $telegramMessage): void
    {
        Notification::send('', new UnknownMessageReceived($telegramMessage));
        Redis::set($telegramMessage->author->id, $telegramMessage->text, 3600);
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
                    case TelegramCallback::Calendize:
                        $this->handleCalendizeCallback($user, (int) $parameter);
                        break;
                }
                break;
            }
        }
    }

    private function handleCalendize(User $user, string $text): void
    {
        if (Str::length($text) < 5) {
            $user->notify(new CustomMesssage("That's not much i can calendize here... are you sure to have included all the information?"));

            return;
        }

        if (Gate::forUser($user)->denies('errors-under-threshold')) {
            $user->notify(new CustomMesssage('You have generated more errors than credits - this is suspected as spam. Please top-up your account to reset the error count.'));

            return;
        }

        if (Gate::forUser($user)->denies('has-credits')) {
            $user->notify(new CreditsRemaining());

            return;
        }

        $icsEventService = new IcsEventService();

        $user->notify(new CustomMesssage('Got it! Give me 10 seconds to process that...'));
        $icsEventService->createIcsEvent(userId: $user->id, prompt: $text, dispatchJob: true);
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
        $events = $user->processedIcsEvents()->latest()->paginate(6, page: $page);
        $user->notify(new MyEvents($events));
    }

    private function handleMyCredits(User $user): void
    {
        $user->notify(new CreditsRemaining());
    }

    private function handleCalendizeCallback(User $user, $redisKey): void
    {
        $prompt = Redis::get($redisKey);

        if (!$prompt) {
            $user->notify(new CustomMesssage("Sorry, i have forgot about that data...\n I only remember what you told me less than 1 hour ago 😊 \nCould you please re-send it to me?"));

            return;
        }

        $this->handleCalendize($user, $prompt);
    }

    private function handleGetEvent(User $user, int $eventId): void
    {
        /** @var IcsEvent|null $event */
        $event = $user->icsEvents->find($eventId);

        if ($event === null) {
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
