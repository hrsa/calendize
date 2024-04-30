<?php

namespace Database\Factories;

use App\Models\IcsEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class IcsEventFactory extends Factory
{
    protected $model = IcsEvent::class;

    public function definition(): array
    {
        return [
            'token_usage' => null,
            'prompt' => $this->faker->words(30, true),
            'error' => null,
            'ics' => null,
            'timezone' => $this->faker->timezone,
            'secret' => Str::random(32),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::factory(),
        ];
    }

    public function icsProcessed(): static
    {
        return $this->state(function (array $attrs) {
            $timezone = $this->faker->timezone;
            $dateTime = Carbon::create($this->faker->dateTimeThisMonth());

            $ics = <<< ICSEVENTDATA
            BEGIN:VCALENDAR
            VERSION:2.0
            PRODID:-//Calendar//Calendize 2.0//EN
            BEGIN:VEVENT
            SUMMARY: {$this->faker->words(4, true)}
            DTSTART;TZID={$timezone}:{$dateTime}
            DTEND;TZID={$timezone}:{$dateTime->addHours($this->faker->numberBetween(1, 3))}
            DESCRIPTION:{$this->faker->words(14, true)}
            END:VEVENT
            END:VCALENDAR
        ICSEVENTDATA;

            return compact('ics');
        });
    }

    public function icsError(): static
    {
        return $this->state(fn (array $attributes) => [
            'error' => $this->faker->words(8, true),
        ]);
    }
}
