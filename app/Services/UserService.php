<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserService
{
    public function createGuestWithCredits(string $email, ?string $password = null, ?int $credits = 1): User
    {
        return User::create([
            'email' => $email,
            'password' => bcrypt($password ?? Str::random(10)),
            'has_password' => false,
            'credits' => $credits,
        ]);
    }

    public function createSubscriptionLink(User $user, string $subscriptionType): string
    {
        return $user->subscribe(config("lemon-squeezy.sales.{$subscriptionType}.variant"))
            ->withThankYouNote('Thanks for joining my adventure!')
            ->redirectTo(route('dashboard', ['payment' => $subscriptionType]))
            ->url();
    }

    public function createTopUpCreditsLink(User $user): string
    {
        return $user->checkout(config('lemon-squeezy.sales.topup.variant'))
            ->withThankYouNote('Thanks for trusting us!')
            ->redirectTo(route('dashboard', ['payment' => 'credits']))
            ->url();
    }

    public function createOrGetSocialiteUser(string $email, string $name, string $provider, string $provider_id)
    {
        $user = User::where(compact('provider', 'provider_id'))
            ->orWhere('email', $email)->first();

        if ($user->provider_id !== $provider_id) {
            $user->update(compact('provider', 'provider_id'));
        }

        if (!$user) {
            $user = User::create([
                'email' => $email,
                'name' => $name,
                'provider' => $provider,
                'provider_id' => $provider_id,
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt(Str::random(10)),
                'has_password' => false,
                'credits' => 5,
            ]);
        }

        return $user;
    }
}
