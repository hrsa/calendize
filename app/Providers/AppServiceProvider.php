<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
        DB::prohibitDestructiveCommands(App::isProduction());

        Gate::define('has-credits', fn (User $user) => $user->credits > 0);

        Gate::define('errors-under-threshold', fn (User $user) => (!$user->failed_requests) || ($user->failed_requests < $user->credits));

        Gate::define('is-admin', fn (User $user) => $user->email === Config::string('app.admin.email'));

        Http::macro('mistral', function () {
            return Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . config('mistral.api_key'),
            ])->baseUrl('https://api.mistral.ai/v1/');
        });

        Http::macro('googlePlaces', function (array $data = []) {
            return Http::withQueryParameters(['key' => Config::string('services.google.places_api_key'), ...$data])
                ->get('https://maps.googleapis.com/maps/api/geocode/json');
        });

        Http::macro('timeZone', function (string $latitude, string $longitude) {
            return Http::withQueryParameters(['key' => Config::string('services.google.places_api_key'),
                'timestamp'                         => time(), 'location' => $latitude . ',' . $longitude])
                ->get('https://maps.googleapis.com/maps/api/timezone/json');
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject(config('app.name') . ' - verify your email address')
                ->greeting('Hi and thanks for your interest in Calendize!')
                ->line("I'll be able to process your events as soon as we confirm you're a real human! 😊")
                ->line('Please, click the button below to verify your email address.')
                ->action('Verify Email Address', $url)
                ->line("I'm already looking forward to working with you.")
                ->line('With the warmest regards,')
                ->salutation('Cally');
        });
    }
}
