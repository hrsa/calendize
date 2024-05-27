<?php

namespace App\Services;

use App\Jobs\GenerateCalendarJob;
use App\Mail\ForwardEmail;
use App\Models\IcsEvent;
use App\Models\User;
use BeyondCode\Mailbox\InboundEmail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailProcessingService
{
    /**
     * @param InboundEmail $email
     */
    public function process(InboundEmail $email): void
    {
        $user = User::whereEmail($email->from())->first();

        if (!$user) {
            return;
        }

        if (Gate::forUser($user)->allows('has-credits')
            && Gate::forUser($user)->allows('errors-under-threshold')
            && IcsEvent::whereEmailId($email->id())->doesntExist()
        ) {
            $icsEvent = IcsEvent::create([
                'user_id'  => $user->id,
                'prompt'   => $email->text(),
                'secret'   => Str::random(32),
                'email_id' => $email->id(),
            ]);

            GenerateCalendarJob::dispatch($icsEvent);
        }
    }

    public function forwardToAdmin(InboundEmail $email): void
    {
        Mail::to(config('app.admin.email'))->send(new ForwardEmail($email));
    }
}
