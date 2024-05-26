<?php

namespace App\Pulse;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Process;
use Laravel\Pulse\Events\SharedBeat;
use Laravel\Pulse\Pulse;

class ComposerOutdatedRecorder
{
    public string $listen = SharedBeat::class;

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    ) {
        //
    }

    public function record(SharedBeat $event)
    {
                if ($event->time !== $event->time->startOfDay()) {
                    return;
                }

        $outdated = Process::run('composer outdated -D -f json');

        $outdated = $outdated->output();

        json_decode($outdated, flags: JSON_THROW_ON_ERROR);

        $this->pulse->set(type: 'composer-outdated', key: 'packages', value: $outdated, timestamp: now());
    }
}
