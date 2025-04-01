<?php

namespace App\Services\Telegram;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Enums\TelegramCommand;
use App\Models\SpamEmail;
use App\Models\User;
use App\Notifications\Telegram\User\CreditsRemaining;
use App\Notifications\Telegram\User\CustomMessage;
use App\Notifications\Telegram\User\MyEvents;
use App\Services\IcsEventService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TelegramCommandHandler
{
    public function __construct(public User $user, public IncomingTelegramMessage $telegramMessage) {}

    public function handleCommand(): void
    {
        switch ($this->telegramMessage->command) {
            case TelegramCommand::Calendize:
                $this->handleCalendize($this->user, $this->telegramMessage->text);
                break;
            case TelegramCommand::NotifyMe:
                $this->handleNotifyMe($this->user);
                break;
            case TelegramCommand::DontNotifyMe:
                $this->handleDontNotifyMe($this->user);
                break;
            case TelegramCommand::MyCredits:
                $this->handleMyCredits($this->user);
                break;
            case TelegramCommand::MyEvents:
                $this->handleMyEvents($this->user);
                break;
            case TelegramCommand::Spam:
                $this->handleSpam($this->user, $this->telegramMessage->text);
            default:
                return;
        }
    }

    public function handleCalendize(User $user, string $text): void
    {
        if (Str::length($text) < 5) {
            $user->notify(new CustomMessage("That's not much i can calendize here... are you sure to have included all the information?"));

            return;
        }

        if (Gate::forUser($user)->denies('has-credits')) {
            $user->notify(new CreditsRemaining);

            return;
        }

        $icsEventService = new IcsEventService;

        $user->notify(new CustomMessage('Got it! Give me 10 seconds to process that...'));
        $icsEventService->createIcsEvent(userId: $user->id, prompt: $text, dispatchJob: true);
    }

    private function handleNotifyMe(User $user): void
    {
        $user->update(['send_tg_notifications' => true]);
        $user->notify(new CustomMessage("Great! Now I'll send you calendized events via Telegram! 😎"));
    }

    private function handleDontNotifyMe(User $user): void
    {
        $user->update(['send_tg_notifications' => false]);
        $user->notify(new CustomMessage('Alright... no more notifications about calendized events!'));
    }

    public function handleMyEvents(User $user, int $page = 1): void
    {
        $events = $user->processedIcsEvents()->latest()->paginate(6, page: $page);
        $user->notify(new MyEvents($events));
    }

    private function handleMyCredits(User $user): void
    {
        $user->notify(new CreditsRemaining);
    }

    private function handleSpam(User $user, string $email): void
    {
        ray($user, $email);
        if ($user->is_admin && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            SpamEmail::create(['email' => $email]);
            $user->notify(new CustomMessage("Thanks for reporting that email! I've added it to the list of spam emails."));
        }
    }
}
