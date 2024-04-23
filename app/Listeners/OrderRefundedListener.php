<?php

namespace App\Listeners;

use LemonSqueezy\Laravel\Events\OrderRefunded;
use LemonSqueezy\Laravel\Order;

class OrderRefundedListener
{

    /**
     * Handle the event.
     */
    public function handle(OrderRefunded $event): void
    {
        ray($event);
        $lmSqueezyOrder = Order::find($event->order->id);

        if ($lmSqueezyOrder?->refunded() && $lmSqueezyOrder->variant_id == config('lemon-squeezy.sales.topup.variant')) {
            $user = $lmSqueezyOrder->billable()->first();
            $user->decrement('credits', 5);
        }

    }
}
