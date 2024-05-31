<?php

namespace App\Listeners;

use App\Enums\LemonSqueezyProduct;
use App\Models\User;
use App\Services\UserService;
use LemonSqueezy\Laravel\Events\OrderCreated;
use LemonSqueezy\Laravel\Order;

class OrderCreatedListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event, UserService $userService): void
    {
        /** @var Order $lmSqueezyOrder */
        $lmSqueezyOrder = Order::find($event->order->id);

        if ($lmSqueezyOrder->paid() && $lmSqueezyOrder->hasVariant(LemonSqueezyProduct::TopUp->variant())) {

            /** @var User $user */
            $user = $lmSqueezyOrder->billable()->first();
            $userService->handleTopUp($user, LemonSqueezyProduct::TopUp->credits());
        }

    }
}
