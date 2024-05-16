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

            $ics = "BEGIN:VCALENDAR\n" .
                "VERSION:2.0\n" .
                "PRODID:-//Calendar//Calendize 2.0//EN\n" .
                "BEGIN:VEVENT\n" .
                "SUMMARY: {$this->faker->words(4, true)}\n" .
                "DTSTART;TZID={$timezone}:{$dateTime}\n" .
                "DTEND;TZID={$timezone}:{$dateTime->addHours($this->faker->numberBetween(1, 3))}\n" .
                "DESCRIPTION:{$this->faker->words(14, true)}\n" .
                "END:VEVENT\n" .
                'END:VCALENDAR';

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
