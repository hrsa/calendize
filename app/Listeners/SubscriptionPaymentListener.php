<?php

namespace App\Listeners;

use App\Enums\LemonSqueezyProduct;
use App\Models\User;
use LemonSqueezy\Laravel\Events\SubscriptionPaymentSuccess;
use LemonSqueezy\Laravel\Subscription;

class SubscriptionPaymentListener
{
    public function handle(SubscriptionPaymentSuccess $event): void
    {
        $lmSqueezySubscription = Subscription::find($event->subscription->id);

        /* @var $user User */
        $user = $lmSqueezySubscription->billable()->first();

        foreach (LemonSqueezyProduct::cases() as $product) {
            if ($product === LemonSqueezyProduct::TopUp) {
                continue;
            }

            if ($lmSqueezySubscription->variant_id == $product->variant()) {

                $rollover = $user->rollover_credits ?? $product->rollover();

                if ($user->credits > $rollover) {
                    $user->credits = $rollover + $product->credits();
                } else {
                    $user->credits += $product->credits();
                }

                $user->failed_requests = 0;
                break;
            }
        }

        $user->save();
    }
}
