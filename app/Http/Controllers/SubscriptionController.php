<?php

namespace App\Http\Controllers;

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
            'renewsAt' => $subscription->renews_at,
            'endsAt' => $subscription->ends_at,
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
}
