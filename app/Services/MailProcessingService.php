<?php

namespace App\Services;

use App\Mail\ForwardEmail;
use App\Mail\NoMoreCreditsError;
use App\Models\IcsEvent;
use App\Models\User;
use BeyondCode\Mailbox\InboundEmail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class MailProcessingService
{
    public function process(InboundEmail $email, IcsEventService $icsEventService): void
    {
        $user = User::whereEmail($email->from())->first();

        if (!$user || IcsEvent::whereEmailId($email->id())->exists()) {
            return;
        }

        if (Gate::forUser($user)->allows('has-credits')) {
            $icsEventService->createIcsEvent(userId: $user->id, prompt: $email->text(), emailId: $email->id(), dispatchJob: true);
        } else {
            $icsEventService->createIcsEvent(userId: $user->id, prompt: $email->text(), emailId: $email->id());
            Mail::to($user->email)->send(new NoMoreCreditsError());
        }
    }

    public function forwardToAdmin(InboundEmail $email): void
    {
        Mail::to(config('app.admin.email'))->send(new ForwardEmail($email));
    }
}
