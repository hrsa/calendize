<?php

namespace App\Services\Telegram;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Enums\TelegramCommand;
use App\Models\User;
use App\Notifications\Telegram\Admin\UnknownMessageReceived;
use App\Notifications\Telegram\ReplyToUnknownUser;
use App\Notifications\Telegram\User\ReplyToUnknownCommand;
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
            $this->replyToUnknownUser($telegramMessage);

            return;
        }

        if (!$telegramMessage->hasCommand() && $telegramMessage->hasText()) {
            $this->forwardToAdminAndReply($user, $telegramMessage);

            return;
        }

        if ($telegramMessage->hasCallbackData()) {
            $telegramCallbackQueryHandler = new TelegramCallbackQueryHandler($user, $telegramMessage);
            $telegramCallbackQueryHandler->handleCallbackQuery();

            return;
        }

        if ($telegramMessage->hasCommand()) {
            $telegramCommandHandler = new TelegramCommandHandler($user, $telegramMessage);
            $telegramCommandHandler->handleCommand();
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

    public function forwardToAdminAndReply(User $user, IncomingTelegramMessage $telegramMessage): void
    {
        Notification::route('telegram', config('app.admin.telegram_chat_id'))->notify(new UnknownMessageReceived($telegramMessage));
        Redis::set($telegramMessage->author->id, $telegramMessage->text, 3600);
        $user->notify(new ReplyToUnknownCommand($telegramMessage));
    }

    private function replyToUnknownUser(IncomingTelegramMessage $telegramMessage): void
    {
        Notification::route('telegram', $telegramMessage->author->id)->notify(new ReplyToUnknownUser($telegramMessage->author));
        Notification::route('telegram', config('app.admin.telegram_chat_id'))->notify(new UnknownMessageReceived($telegramMessage));
    }
}
