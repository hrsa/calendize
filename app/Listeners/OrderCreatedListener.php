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
        $lmSqueezyOrder = Order::find($event->order->id);

        /** @var Order $lmSqueezyOrder */
        if ($lmSqueezyOrder?->paid() && $lmSqueezyOrder->variant_id == LemonSqueezyProduct::TopUp->variant()) {

            /* @var $user User */
            $user = $lmSqueezyOrder->billable()->first();
            $user->increment('credits', LemonSqueezyProduct::TopUp->credits());
            $user->update(['failed_requests' => 0]);
        }

    }
}
