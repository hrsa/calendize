<?php

namespace App\Listeners;

use App\Enums\LemonSqueezyProduct;
use App\Models\User;
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

        /** @var Order $lmSqueezyOrder */
        if ($lmSqueezyOrder?->refunded() && $lmSqueezyOrder->variant_id == LemonSqueezyProduct::TopUp->variant()) {

            /* @var $user User */
            $user = $lmSqueezyOrder->billable()->first();
            $user->decrement('credits', LemonSqueezyProduct::TopUp->credits());
        }

    }
}
