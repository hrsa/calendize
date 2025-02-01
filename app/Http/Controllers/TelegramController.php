<?php

namespace App\Http\Controllers;

use App\Data\Telegram\IncomingTelegramMessage;
use App\Data\Telegram\IncomingTelegramMessageAuthor;
use App\Data\Telegram\IncomingTelegramMessageLocation;
use App\Notifications\Telegram\User\CustomMessage;
use App\Services\Telegram\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        Auth::user()->notifyNow(new CustomMessage('Congratulations! Your Calendize account is now connected 😊'));

        return Inertia::render('Generate', [
            'serverSuccess'      => 'Your Telegram account is successfully connected!',
            'serverErrorMessage' => null,
            'eventId'            => null,
            'eventSecret'        => null,
        ]);
    }

    public function processWebhook(TelegramService $telegramService): void
    {
        if (request()->header('x-telegram-bot-api-secret-token') !== config('services.telegram-bot-api.header-token')) {
            return;
        }

        $payload = json_decode(request()->getContent());
        $telegramMessage = $payload->message ?? $payload->edited_message ?? $payload->callback_query;

        try {
            $telegramMessageData = new IncomingTelegramMessage(
                author: new IncomingTelegramMessageAuthor(
                    $telegramMessage->from->id,
                    $telegramMessage->from->is_bot,
                    $telegramMessage->from->is_premium ?? false,
                    $telegramMessage->from->first_name,
                    $telegramMessage->from->last_name ?? null,
                    $telegramMessage->from->username,
                    $telegramMessage->from->language_code
                ),
                text: $telegramMessage->text ?? $telegramMessage->caption ?? null,
                data: $telegramMessage->data ?? null,
                location: isset($telegramMessage->location)
                    ? new IncomingTelegramMessageLocation(
                        $telegramMessage->location->latitude,
                        $telegramMessage->location->longitude
                    )
                    : null
            );
            $telegramService->process($telegramMessageData);
        } catch (\Exception $e) {
            Log::error('Error creating Telegram message data', [
                'exception' => $e->getMessage(),
                'payload'   => request()->getContent(),
            ]);
        }
    }
}
