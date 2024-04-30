<?php

namespace App\Events;

use App\Exceptions\NoSummaryException;
use App\Models\IcsEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IcsEventProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public IcsEvent $icsEvent)
    {
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     *
     * @throws NoSummaryException
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->icsEvent->id,
            'summary' => $this->icsEvent->ics ? $this->icsEvent->getSummary() : null,
            'ics' => $this->icsEvent->ics,
            'secret' => $this->icsEvent->secret,
            'error' => $this->icsEvent->error,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ics-event-' . $this->icsEvent->id),
        ];
    }
}
