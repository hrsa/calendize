<?php

namespace App\Providers;

use App\Services\MailProcessingService;
use BeyondCode\Mailbox\Facades\Mailbox;
use BeyondCode\Mailbox\InboundEmail;
use Illuminate\Support\ServiceProvider;

class MailProcessingServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(MailProcessingService $service): void
    {
        Mailbox::to('ics@calendize.it', fn(InboundEmail $inboundEmail) => $service->process($inboundEmail));
        Mailbox::to('hey@calendize.it', fn(InboundEmail $inboundEmail) => $service->process($inboundEmail));
        Mailbox::to('contact@calendize.it', fn(InboundEmail $inboundEmail) => $service->forwardToAdmin($inboundEmail));
        Mailbox::to('cally@calendize.it', fn(InboundEmail $inboundEmail) => $service->forwardToAdmin($inboundEmail));
    }
}
