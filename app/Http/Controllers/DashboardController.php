<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(UserService $userService): \Inertia\Response
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
        } else {
            $paymentConfirmation = null;
        }

        switch ($payment) {
            case 'credits':
                $paymentConfirmation['title'] = 'Your payment was successful!';
                $paymentConfirmation['content'] = "Thanks! I'm looking forward to working with you!";
                break;
            case 'beginner':
                $paymentConfirmation['title'] = 'beginner';
                $paymentConfirmation['content'] = 'Thanks! beginner now!';
                break;
            case 'classic':
                $paymentConfirmation['title'] = 'classic';
                $paymentConfirmation['content'] = 'Thanks! classic now!';
                break;
            case 'power':
                $paymentConfirmation['title'] = 'power';
                $paymentConfirmation['content'] = 'Thanks! power now!';
                break;
        }

        return Inertia::render('Dashboard', compact('buyCreditsLink', 'paymentConfirmation'));
    }
}
