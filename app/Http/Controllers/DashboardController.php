<?php

namespace App\Http\Controllers;

use App\Enums\LemonSqueezyProduct;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(UserService $userService): Response
    {
        $products = array_map(fn ($product) => $product->value, LemonSqueezyProduct::cases());

        request()->validate([
            'payment' => Rule::in($products),
        ]);

        $payment = request('payment');

        $user = Auth::user();

        $buyCreditsLink = $userService->createTopUpCreditsLink($user);

        if ($payment) {
            $paymentConfirmation = [
                'imageSrc' => "/{$payment}.png",
                'imageAlt' => 'Calendize',
            ];

            $paymentProduct = LemonSqueezyProduct::from($payment);
            $paymentConfirmation['title'] = __("dashboard.popup.{$paymentProduct->value}.title");
            $paymentConfirmation['content'] = __("dashboard.popup.{$paymentProduct->value}.content");
        } else {
            $paymentConfirmation = null;
        }

        return Inertia::render('Dashboard', compact('buyCreditsLink', 'paymentConfirmation'));
    }
}
