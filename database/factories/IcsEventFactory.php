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
}
