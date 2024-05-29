<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\Telegram\Admin\NewUserCreated;
use App\Notifications\Telegram\User\CreditsRemaining;
use Illuminate\Support\Facades\Notification;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if (config('app.env') === 'production') {
            Notification::send('', new NewUserCreated($user));
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if (config('app.env') === 'production' && $user->wasChanged('credits') && $user->credits < 3 && $user->telegram_id && $user->send_tg_notifications) {
            $user->notify(new CreditsRemaining());
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
