<?php

namespace App\Services;

use App\Data\MailgunEmail;
use App\Mail\ForwardEmail;
use App\Mail\NoMoreCreditsError;
use App\Models\IcsEvent;
use App\Models\SpamEmail;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class MailProcessingService
{
    public function __construct(public IcsEventService $icsEventService) {}

    public function process(MailgunEmail $email): void
    {
        $user = User::whereEmail($email->sender)->first();
        if (!$user || IcsEvent::whereEmailId($email->messageId)->exists()) {
            return;
        }

        $messageText = $email->body ?? $email->strippedText ?? $email->bodyHtml;

        if (Gate::forUser($user)->allows(ability: 'has-credits')) {
            $this->icsEventService->createIcsEvent(userId: $user->id, prompt: $messageText, emailId: $email->messageId, dispatchJob: true);
        } else {
            $this->icsEventService->createIcsEvent(userId: $user->id, prompt: $messageText, emailId: $email->messageId);
            Mail::to($user->email)->send(new NoMoreCreditsError);
        }
    }

    public function forwardToAdmin(MailgunEmail $email): void
    {
        if (SpamEmail::whereEmail($email->sender)->doesntExist()) {
            Mail::to(Config::string('app.admin.email'))->send(new ForwardEmail($email));
        }
    }
}
