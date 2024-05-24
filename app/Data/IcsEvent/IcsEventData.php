<?php

namespace App\Data\IcsEvent;

use Spatie\LaravelData\Data;

/**
 * @property Person[] $attendees
 */
class IcsEventData extends Data
{
    public function __construct(
        public Date $starts,
        public Date $ends,
        public string $name,
        public ?string $description = null,
        public ?string $address = null,
        public ?string $addressName = null,
        public ?string $url = null,
        public ?string $googleConference = null,
        public ?string $microsoftTeams = null,
        public ?Person $organizer = null,
        public ?array $attendees = null,
        public ?bool $isFullDay = null,
        public ?RecurrenceRule $rrule = null,
    ) {
        $this->isFullDay ??= false;
    }
}
