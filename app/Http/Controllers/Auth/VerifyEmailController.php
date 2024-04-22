<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCalendarJob;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('generate', absolute: false));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            $request->user()->update(['credits' => 5]);
            $icsEvent = $request->user()->icsEvents->first();
            if ($icsEvent && !$icsEvent->ics && !$icsEvent->error) {
                GenerateCalendarJob::dispatch($icsEvent);
            }
        }
        return redirect()->to(route('generate', [
            'serverSuccess' => $icsEvent && $icsEvent->ics
                ? "Your email is verified, thank you! I've also gave you some free credits to start. Here is your event:  " . $icsEvent->getSummary()
                : "Your email is verified, thank you! I've also gave you some free credits to start.",
            'serverErrorMessage' => $icsEvent ? $icsEvent->error : null,
            'eventId' => $icsEvent ? $icsEvent->id : null,
            'eventSecret' => $icsEvent ? $icsEvent->secret : null,
            ], absolute: false));
    }
}
