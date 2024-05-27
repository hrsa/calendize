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
        /** @var Order $lmSqueezyOrder */
        $lmSqueezyOrder = Order::find($event->order->id);

        if ($lmSqueezyOrder->refunded() && $lmSqueezyOrder->hasVariant(LemonSqueezyProduct::TopUp->variant())) {

            /** @var User $user */
            $user = $lmSqueezyOrder->billable()->first();
            $user->decrement('credits', LemonSqueezyProduct::TopUp->credits());
        }

    }
}
