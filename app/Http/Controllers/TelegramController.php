<?php

namespace App\Http\Controllers;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Data\Telegram\IncomingTelegramMessageAuthor;
use App\Notifications\Telegram\User\CustomMesssage;
use App\Services\Telegram\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TelegramController extends Controller
{
    public function connectTelegram(): Response|RedirectResponse
    {
        if (!request()->query('tgid')) {
            return redirect()->to(route('login'));
        }

        $telegramId = (base64_decode(request()->query('tgid')));

        Auth::user()->update(['telegram_id' => $telegramId, 'send_tg_notifications' => true]);

        Auth::user()->notifyNow(new CustomMesssage('Congratulations! Your Calendize account is now connected 😊'));

        return Inertia::render('Generate', [
            'serverSuccess'      => 'Your Telegram account is successfully connected!',
            'serverErrorMessage' => null,
            'eventId'            => null,
            'eventSecret'        => null,
        ]);
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

        $telegramService->process($telegramMessageData);
    }
}
