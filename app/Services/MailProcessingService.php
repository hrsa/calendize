<?php

namespace App\Services;

use App\Jobs\GenerateCalendarJob;
use App\Models\IcsEvent;
use App\Models\User;
use BeyondCode\Mailbox\InboundEmail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class MailProcessingService
{
    public function process(InboundEmail $message): void
    {
        $user = User::where('email', $message->from())->first();

        if (!$user) {
            return;
        }

        if (Gate::forUser($user)->allows('has-credits')
            && Gate::forUser($user)->allows('is-not-blocked')
        ) {

            ray("We've got mail", "FROM", $message->from(), $message->fromName(), "ABOUT", $message->html(), "The User has credits:", $user->credits);

            $icsEvent = IcsEvent::create([
                'user_id' => $user->id,
                'prompt' => strip_tags($message->html())
            ]);

            GenerateCalendarJob::dispatch($icsEvent);
        }
    }
}
