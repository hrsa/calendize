<?php

namespace App\Listeners;

use LemonSqueezy\Laravel\Events\OrderRefunded;
use LemonSqueezy\Laravel\Order;

/**
 * @property string $variant_id
 *
 * @mixin Order
 */
class OrderRefundedListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderRefunded $event): void
    {
        $lmSqueezyOrder = Order::find($event->order->id);

        if ($lmSqueezyOrder?->refunded() && $lmSqueezyOrder->variant_id == config('lemon-squeezy.sales.topup.variant')) {

            /* @var $user \App\Models\User */
            $user = $lmSqueezyOrder->billable()->first();
            $user->decrement('credits', config('lemon-squeezy.sales.topup.credits'));
        }

    }
}
