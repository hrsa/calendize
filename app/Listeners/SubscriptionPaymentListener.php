<?php

namespace App\Listeners;

use App\Enums\LemonSqueezyProduct;
use App\Models\User;
use App\Services\UserService;
use LemonSqueezy\Laravel\Events\SubscriptionPaymentSuccess;
use LemonSqueezy\Laravel\Subscription;

class SubscriptionPaymentListener
{
    public function handle(SubscriptionPaymentSuccess $event, UserService $userService): void
    {
        /** @var Subscription $lmSqueezySubscription */
        $lmSqueezySubscription = Subscription::find($event->subscription->id);

        /** @var User $user */
        $user = $lmSqueezySubscription->billable()->first();

        foreach (LemonSqueezyProduct::subscriptions() as $product) {
            if ($lmSqueezySubscription->hasVariant($product->variant())) {
                $userService->handleSubscriptionPayment($user, $product);
                break;
            }
        }

        $user->save();
    }
}
