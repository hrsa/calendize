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
                $paymentConfirmation['title']   = __('dashboard.popup.credits.title');
                $paymentConfirmation['content'] = __('dashboard.popup.credits.content');
                break;
            case 'beginner':
                $paymentConfirmation['title']   = __('dashboard.popup.beginner.title');
                $paymentConfirmation['content'] = __('dashboard.popup.beginner.content');
                break;
            case 'classic':
                $paymentConfirmation['title']   = __('dashboard.popup.classic.title');
                $paymentConfirmation['content'] = __('dashboard.popup.classic.content');
                break;
            case 'power':
                $paymentConfirmation['title']   = __('dashboard.popup.power.title');
                $paymentConfirmation['content'] = __('dashboard.popup.power.content');
                break;
        }

        return Inertia::render('Dashboard', compact('buyCreditsLink', 'paymentConfirmation'));
    }
}
