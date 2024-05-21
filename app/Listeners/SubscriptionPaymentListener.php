<?php

namespace App\Listeners;

use LemonSqueezy\Laravel\Events\SubscriptionPaymentSuccess;
use LemonSqueezy\Laravel\Subscription;

class SubscriptionPaymentListener
{
    public function handle(SubscriptionPaymentSuccess $event): void
    {
        $lmSqueezySubscription = Subscription::find($event->subscription->id);

        /* @var $user \App\Models\User */
        $user     = $lmSqueezySubscription->billable()->first();
        $variants = ['beginner', 'classic', 'power'];

        foreach ($variants as $variant) {
            if ($lmSqueezySubscription->variant_id == config("lemon-squeezy.sales.{$variant}.variant")) {

                $rollover = $user->rollover_credits ?? config("lemon-squeezy.sales.{$variant}.rollover");

                if ($user->credits > $rollover) {
                    $user->credits = $rollover + config("lemon-squeezy.sales.{$variant}.credits");
                } else {
                    $user->credits += config("lemon-squeezy.sales.{$variant}.credits");
                }

                $user->failed_requests = 0;
                break;
            }
        }

        $user->save();
    }
}
