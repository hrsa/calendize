<?php

namespace App\Providers;

use App\Services\MailProcessingService;
use BeyondCode\Mailbox\Facades\Mailbox;
use Illuminate\Support\ServiceProvider;

class MailProcessingServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(MailProcessingService $service): void
    {
        Mailbox::to('ics@calendize.it', function ($message) use ($service) {
            $service->process($message);
        });
    }
}
