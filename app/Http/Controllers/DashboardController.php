<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(bool $paymentSucceeded = false): \Inertia\Response
    {
        $paymentSucceeded = request('payment');

        $user = Auth::user();

        $activeSubscription = match ($user->subscriptions()->active()->first()?->variant_id) {
            config('lemon-squeezy.sales.beginner.variant') => 'beginner',
            config('lemon-squeezy.sales.classic.variant') => 'classic',
            config('lemon-squeezy.sales.power.variant') => 'power',
            default => 'none',
        };

       $buyCreditsLink = $user->checkout(config("lemon-squeezy.sales.topup.variant"))
                                ->withThankYouNote("Thanks for trusting us!")
                                ->redirectTo(route('dashboard', ['payment' => 'success']))
                                ->url();

        $paymentConfirmation = $paymentSucceeded
            ? [
            'title' => 'Your payment was successful!',
            'content' => "Thanks! I'm looking forward to working with you!",
            'imageSrc' => '/calendar.png',
            'imageAlt' => 'Calendize'
            ]
            : null;

        return Inertia::render('Dashboard', compact('activeSubscription', 'buyCreditsLink', 'paymentConfirmation'));
    }
}
