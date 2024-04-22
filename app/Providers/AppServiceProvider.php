<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('has-credits', fn(User $user) => $user->credits > 0);

        Gate::define('is-not-blocked', fn(User $user) => !$user->blocked);

        Http::macro('mistral', function () {
            return Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . config('mistral.api_key')
            ])->baseUrl('https://api.mistral.ai/v1/');
        });
    }
}
