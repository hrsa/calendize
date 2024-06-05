<?php

namespace App\Helpers;

use App\Data\IcsEvent\IcsEventData;
use App\Data\IcsEvent\IcsEventsArray;
use App\Data\IcsEvent\Person;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class IcsGenerator
{
    /**
     * @throws Exception
     */
    public static function generateEvent(IcsEventData $data): Event
    {
        $event = Event::create()
            ->name($data->name)
            ->createdAt(now())
            ->description($data->description)
            ->uniqueIdentifier(Str::random(6) . '@Calendize')
            ->alertMinutesBefore(60)
            ->startsAt(new DateTime($data->starts->at))
            ->endsAt(new DateTime($data->ends->at));

        if ($data->starts->timezone) {
            $event->startsAt(new DateTime($data->starts->at, new DateTimeZone($data->starts->timezone)));
        }

        if ($data->ends->timezone) {
            $event->endsAt(new DateTime($data->ends->at, new DateTimeZone($data->ends->timezone)));
        }

        if ($data->address) {
            $event->address($data->address);
            try {
                $googleAddress = AddressProcessor::getCoordinatesFromAddress($data->address);
                $event->address($googleAddress->address);
                $event->coordinates((float) $googleAddress->lat, (float) $googleAddress->lng);
            } catch (Exception $e) {
                Log::alert("Error getting coordinates for {$data->address} - {$e->getMessage()}");
            }
        }

        if ($data->addressName) {
            $event->addressName($data->addressName);
        }

        if ($data->url) {
            $event->url($data->url);
        }

        if ($data->googleConference) {
            $event->googleConference($data->googleConference);
        }

        if ($data->microsoftTeams) {
            $event->microsoftTeams($data->microsoftTeams);
        }

        if ($data->organizer instanceof Person) {
            $event->organizer($data->organizer->email, $data->organizer->name);
        }

        foreach ($data->attendees as $attendee) {
            $event->attendee($attendee->email, $attendee->name);
        }

        if ($data->rrule?->frequency) {
            $rRule = new RRule(RecurrenceFrequency::from($data->rrule->frequency));

            if ($data->rrule->times && $data->rrule->interval && ($data->rrule->until === null || $data->rrule->until === '' || $data->rrule->until === '0')) {
                $rRule->times((int) $data->rrule->times);
            }

            if ($data->rrule->interval) {
                $rRule->interval((int) $data->rrule->interval);
            }

            if ($data->rrule->starting) {
                $rRule->starting(new DateTime($data->rrule->starting));
            }

            if ($data->rrule->until) {
                $rRule->until(new DateTime($data->rrule->until));
            }

            $event->rrule($rRule);
        }

        if ($data->isFullDay === true) {
            $event->fullDay();
        }

        return $event;
    }

    /**
     * @throws \Exception
     */
    public static function generateCalendar(?IcsEventData $event = null, ?IcsEventsArray $events = null): Calendar
    {

        $calendar = Calendar::create('Calendize calendar')
            ->productIdentifier('Calendize');

        if ($event instanceof IcsEventData) {
            $calendar->event(self::generateEvent($event));
        }

        if ($events instanceof IcsEventsArray) {
            foreach ($events->events as $event) {
                $calendar->event(self::generateEvent($event));
            }
        }

        return $calendar;
    }
}
