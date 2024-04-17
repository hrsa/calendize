<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('generate-ics', function (User $user){

            if ($user->credits = 0) {
                Response::deny("You don't have enough credits");
            }

            if ($user->blocked) {
                Response::deny('You were blocked for too many false requests');
            }

            Response::allow();
        });
    }
}
