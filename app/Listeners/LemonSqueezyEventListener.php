<?php

namespace App\Listeners;

use LemonSqueezy\Laravel\Events\WebhookHandled;
use LemonSqueezy\Laravel\Events\WebhookReceived;

class LemonSqueezyEventListener
{

    /**
     * Handle the event.
     */
    public function handle(WebhookHandled $event): void
    {
        ray($event)->blue();
        ray($event->payload['meta']['event_name'])->blue();
        ray($event->payload['meta']['custom_data']['billable_id'])->orange();

    }
}
