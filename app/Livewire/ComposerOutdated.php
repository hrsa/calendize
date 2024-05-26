<?php

namespace App\Livewire;

use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;

class ComposerOutdated extends Card
{
    public function render()
    {
        $packages = Pulse::values('composer-outdated', ['packages'])->get('packages');

        $packages = $packages
            ? json_decode($packages->value, true, JSON_THROW_ON_ERROR)['installed']
            : [];

        foreach ($packages as &$package) {
            $currentVersionParts = explode('.', $package['version']);
            $latestVersionParts = explode('.', $package['latest']);

            $isBreaking = $latestVersionParts[0] > $currentVersionParts[0];
            $isFeature = $latestVersionParts[1] > $currentVersionParts[1] && !$isBreaking;
            $isMinor = $latestVersionParts[2] > $currentVersionParts[2] && !$isBreaking && !$isFeature;

            $package['updateType'] = $isMinor ? 'minor' : ($isFeature ? 'feature' : ($isBreaking ? 'breaking' : ''));
        }

        return view('livewire.composer-outdated', compact('packages'));
    }
}
