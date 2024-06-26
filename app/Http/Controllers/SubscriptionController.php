<?php

namespace App\Http\Controllers;

use App\Enums\LemonSqueezyProduct;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use LemonSqueezy\Laravel\Subscription;

class SubscriptionController extends Controller
{
    public function subscribe(UserService $userService): JsonResponse
    {
        $subscriptions = array_map(fn ($subscription) => $subscription->value, LemonSqueezyProduct::subscriptions());

        request()->validate([
            'type' => Rule::in($subscriptions),
        ]);

        $subscriptionLink = $userService->createSubscriptionLink(request()->user(), LemonSqueezyProduct::from(request('type')));

        return response()->json($subscriptionLink);
    }

    public function getModificationData(): JsonResponse
    {
        /** @var Subscription|null $subscription */
        $subscription = request()->user()->subscriptions()->whereStatus(Subscription::STATUS_ACTIVE)->first();

        if ($subscription === null) {
            return response()->json(['error' => "You don't have a subscription"], 400);
        }

        return response()->json([
            'renewsAt'         => $subscription->renews_at ?? null,
            'endsAt'           => $subscription->ends_at ?? null,
            'paymentMethodUrl' => $subscription->updatePaymentMethodUrl(),
        ]);
    }

    public function cancel(): JsonResponse
    {
        /** @var Subscription|null $subscription */
        $subscription = request()->user()->subscriptions()->whereStatus(Subscription::STATUS_ACTIVE)->first();

        if ($subscription === null) {
            return response()->json(['error' => "You don't have a subscription"], 400);
        }

        $subscription->cancel();

        return response()->json([
            'endsAt' => $subscription->ends_at ?? null,
        ]);
    }

    public function swap(): JsonResponse
    {
        $subscriptions = array_map(fn ($product) => $product->value, LemonSqueezyProduct::subscriptions());

        request()->validate([
            'newSubscription' => Rule::in($subscriptions),
        ]);

        /** @var Subscription $subscription */
        $subscription = request()->user()->subscriptions()->whereStatus(Subscription::STATUS_ACTIVE)->first();
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
