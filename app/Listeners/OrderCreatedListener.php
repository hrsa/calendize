<?php

namespace App\Listeners;

use App\Enums\LemonSqueezyProduct;
use App\Models\User;
use LemonSqueezy\Laravel\Events\OrderCreated;
use LemonSqueezy\Laravel\Order;

class OrderCreatedListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        /** @var Order $lmSqueezyOrder */
        $lmSqueezyOrder = Order::find($event->order->id);


        if ($lmSqueezyOrder->paid() && $lmSqueezyOrder->hasVariant(LemonSqueezyProduct::TopUp->variant())) {

            /** @var User $user */
            $user = $lmSqueezyOrder->billable()->first();
            $user->increment('credits', LemonSqueezyProduct::TopUp->credits());
            $user->update(['failed_requests' => 0]);
        }

    }
}
