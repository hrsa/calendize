<?php

namespace App\Listeners;

use App\Enums\LemonSqueezyProduct;
use App\Models\User;
use App\Services\UserService;
use LemonSqueezy\Laravel\Events\OrderCreated;
use LemonSqueezy\Laravel\Order;

class OrderCreatedListener
{
    public function __construct(public UserService $userService) {}

    public function handle(OrderCreated $event): void
    {
        /** @var Order $lmSqueezyOrder */
        $lmSqueezyOrder = Order::find($event->order->id);

        if ($lmSqueezyOrder->paid() && $lmSqueezyOrder->hasVariant(LemonSqueezyProduct::TopUp->variant())) {

            /** @var User $user */
            $user = $lmSqueezyOrder->billable()->first();
            $this->userService->handleTopUp($user, LemonSqueezyProduct::TopUp->credits());
        }

    }
}
