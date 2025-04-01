<?php

namespace App\Services;

use App\Mail\ForwardEmail;
use App\Mail\NoMoreCreditsError;
use App\Models\IcsEvent;
use App\Models\SpamEmail;
use App\Models\User;
use BeyondCode\Mailbox\InboundEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class MailProcessingService
{
    public function __construct(public IcsEventService $icsEventService) {}

    public function process(InboundEmail $email): void
    {
        $user = User::whereEmail($email->from())->first();

        if (!$user || IcsEvent::whereEmailId($email->id())->exists()) {
            return;
        }

        if (Gate::forUser($user)->allows('has-credits')) {
            $this->icsEventService->createIcsEvent(userId: $user->id, prompt: $email->text(), emailId: $email->id(), dispatchJob: true);
        } else {
            $this->icsEventService->createIcsEvent(userId: $user->id, prompt: $email->text(), emailId: $email->id());
            Mail::to($user->email)->send(new NoMoreCreditsError);
        }
    }

    public function forwardToAdmin(InboundEmail $email): void
    {
        if (SpamEmail::whereEmail($email->from())->doesntExist()) {
            Mail::to(Config::string('app.admin.email'))->send(new ForwardEmail($email));
        }
    }
}
