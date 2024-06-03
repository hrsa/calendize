<?php

namespace App\Services;

use App\Enums\LemonSqueezyProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class UserService
{
    public function createGuestWithCredits(string $email, ?string $password = null, ?int $credits = 1): User
    {
        return User::create([
            'email'        => $email,
            'password'     => bcrypt($password ?? Str::random(10)),
            'has_password' => false,
            'credits'      => $credits,
        ]);
    }

    public function createSubscriptionLink(User $user, LemonSqueezyProduct $subscription): string
    {
        if (!config('lemon-squeezy.api_key') && !app()->isProduction()) {
            return 'https://fake.link';
        }

        return $user->subscribe($subscription->variant())
            ->withThankYouNote('Thanks for joining my adventure!')
            ->redirectTo(route('dashboard', ['payment' => $subscription->value]))
            ->url();
    }

    public function createTopUpCreditsLink(User $user): string
    {
        if (!Config::string('lemon-squeezy.api_key') && !app()->isProduction()) {
            return 'https://fake.link';
        }

        return $user->checkout(LemonSqueezyProduct::TopUp->variant())
            ->withThankYouNote('Thanks for trusting us!')
            ->redirectTo(route('dashboard', ['payment' => LemonSqueezyProduct::TopUp->value]))
            ->url();
    }

    public function handleTopUp(User $user, int $credits): void
    {
        $user->increment('credits', $credits);
        $user->update(['failed_requests' => 0]);

        $icsEventService = new IcsEventService();
        $icsEventService->processPendingEvents($user);
    }

    public function handleSubscriptionPayment(User $user, LemonSqueezyProduct $product): void
    {
        $rollover = $user->rollover_credits ?? $product->rollover();

        if ($user->credits > $rollover) {
            $user->credits = $rollover + $product->credits();
        } else {
            $user->credits += $product->credits();
        }

        $user->failed_requests = 0;
        $user->save();

        $icsEventService = new IcsEventService();
        $icsEventService->processPendingEvents($user);
    }

    public function createOrGetSocialiteUser(string $email, string $name, string $provider, string $provider_id): User
    {
        $user = User::where(compact('provider', 'provider_id'))
            ->orWhere('email', $email)->first();

        if ($user->provider_id !== $provider_id) {
            $user->update(compact('provider', 'provider_id'));
        }

        if (!$user) {
            $user = User::create([
                'email'             => $email,
                'name'              => $name,
                'provider'          => $provider,
                'provider_id'       => $provider_id,
                'email_verified_at' => Carbon::now(),
                'password'          => bcrypt(Str::random(10)),
                'has_password'      => false,
                'credits'           => 5,
            ]);
        }

        return $user;
    }
}
