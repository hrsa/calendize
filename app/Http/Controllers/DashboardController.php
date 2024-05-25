<?php

namespace App\Http\Controllers;

use App\Enums\LemonSqueezyProduct;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(UserService $userService): Response
    {
        request()->validate([
            'payment' => 'in:credits,beginner,classic,power',
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
