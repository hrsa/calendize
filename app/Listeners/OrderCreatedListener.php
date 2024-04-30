<?php

namespace App\Listeners;

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

        if ($lmSqueezyOrder?->paid() && $lmSqueezyOrder->variant_id == config('lemon-squeezy.sales.topup.variant')) {

            /* @var $user \App\Models\User */
            $user = $lmSqueezyOrder->billable()->first();
            $user->increment('credits', config('lemon-squeezy.sales.topup.credits'));
            $user->update(['failed_requests' => 0]);
        }

    }
}
