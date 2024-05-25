<?php

namespace App\Http\Controllers;

use App\Enums\LemonSqueezyProduct;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function subscribe(UserService $userService): JsonResponse
    {
        $subscriptionLink = $userService->createSubscriptionLink(request()->user(), request('type'));

        return response()->json($subscriptionLink);
    }

    public function getModificationData(): JsonResponse
    {
        $subscription = request()->user()->subscriptions()->active()->first();

        return response()->json([
            'renewsAt'         => $subscription->renews_at,
            'endsAt'           => $subscription->ends_at,
            'paymentMethodUrl' => $subscription->updatePaymentMethodUrl(),
        ]);
    }

    public function cancel(): JsonResponse
    {
        $subscription = request()->user()->subscriptions()->active()->first();
        $subscription->cancel();

        return response()->json([
            'endsAt' => $subscription->ends_at,
        ]);
    }

    public function swap(): JsonResponse
    {
        request()->validate([
            'newSubscription' => 'in:beginner,classic,power',
        ]);

        $subscription = request()->user()->subscriptions()->active()->first();
        $newSubscription = LemonSqueezyProduct::from(request('newSubscription'));

        if (request('swapDate') === 'now') {
            $subscription->swapAndInvoice(
                $newSubscription->product(),
                $newSubscription->variant()
            );
        }

        if (request('swapDate') === 'at renewal') {
            $subscription->swap(
                $newSubscription->product(),
                $newSubscription->variant()
            );
        }

        return response()->json([$subscription]);
    }
}
