<?php

namespace App\Livewire;

use App\Models\IcsEvent;
use App\Models\User;
use Illuminate\Support\Number;
use Laravel\Pulse\Livewire\Card;

class Statistics extends Card
{
    public function render()
    {
        $statistics = [
            'Users'            => User::count(),
            'Active users'     => User::whereHas('icsEvents')->count(),
            'Events'           => IcsEvent::count(),
            'Failed events'    => IcsEvent::whereNotNull('error')->count(),
            'Processed events' => IcsEvent::whereNotNull('ics')->count(),
            'Abandoned events' => IcsEvent::whereNull('ics')->whereNull('error')->count(),
            'Tokens usage'     => IcsEvent::sum('token_usage'),
            'Total costs'      => Number::currency((IcsEvent::sum('token_usage') * 0.00003), 'USD'),
        ];

        return view('livewire.statistics', compact('statistics'));
    }
}
