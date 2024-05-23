<?php

namespace App\Http\Controllers;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Data\Telegram\IncomingTelegramMessageAuthor;
use App\Enums\TelegramCommand;
use App\Notifications\Telegram\User\CustomMesssage;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    public function connectTelegram(): RedirectResponse
    {
        $telegramId = (base64_decode(request()->query('tgid')));

        Auth::user()->update(['telegram_id' => $telegramId]);

        Auth::user()->notifyNow(new CustomMesssage('Congratulations! Your Calendize account is now connected 😊'));

        return redirect()->to(route('generate', [
            'serverSuccess'      => 'Your Telegram account is successfully connected!',
            'serverErrorMessage' => null,
            'eventId'            => null,
            'eventSecret'        => null,
        ], absolute: false));
    }

    public function processWebhook(TelegramService $telegramService)
    {
        if (request()->header('x-telegram-bot-api-secret-token') !== config('services.telegram-bot-api.header-token')) {
            return;
        }

        $payload = json_decode(request()->getContent());
        $telegramMessage = $payload->message ?? $payload->edited_message ?? $payload->callback_query;

        $telegramMessageData = new IncomingTelegramMessage(
            new IncomingTelegramMessageAuthor(
                $telegramMessage->from->id,
                $telegramMessage->from->is_bot,
                $telegramMessage->from->is_premium ?? false,
                $telegramMessage->from->first_name,
                $telegramMessage->from->last_name ?? null,
                $telegramMessage->from->username,
                $telegramMessage->from->language_code
            ),
            $telegramMessage->text ?? null,
            $telegramMessage->data ?? null,
        );

        $this->findCommand($telegramMessageData);

        $telegramService->process($telegramMessageData);

    }

    private function findCommand(IncomingTelegramMessage $incomingTelegramMessage): void
    {
        foreach (TelegramCommand::cases() as $command) {
            if (Str::startsWith($incomingTelegramMessage->text, $command->value)) {
                if (Str::length($incomingTelegramMessage->text) === Str::length($command->value)) {
                    $incomingTelegramMessage->text = '';
                } else {
                    $incomingTelegramMessage->text = Str::of($incomingTelegramMessage->text)->replaceStart($command->value . ' ', '')->value();
                }
                $incomingTelegramMessage->command = $command;
                break;
            }
        }
    }
}
