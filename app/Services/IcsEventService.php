<?php

namespace App\Services;

use App\Jobs\GenerateCalendarJob;
use App\Models\IcsEvent;
use App\Models\User;
use Illuminate\Support\Str;

class IcsEventService
{
    public function createIcsEvent(int $userId, string $prompt, ?string $timezone = null, ?string $emailId = null, ?bool $dispatchJob = false)
    {
        $icsEvent = IcsEvent::create([
            'user_id'  => $userId,
            'secret'   => Str::random(32),
            'prompt'   => $prompt,
            'timezone' => $timezone,
            'email_id' => $emailId,
        ]);

        if ($dispatchJob) {
            GenerateCalendarJob::dispatch($icsEvent);
        }

        return $icsEvent;
    }

    public function processPendingEvents(User $user): void
    {
        $pendingEvents = $user->pendingIcsEvents()->get();

        /** @var IcsEvent $icsEvent */
        foreach ($pendingEvents as $icsEvent) {
            GenerateCalendarJob::dispatch($icsEvent);
        }
    }
}
