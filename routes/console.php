<?php

use Illuminate\Support\Facades\Schedule;

if (config('app.env') === 'production') {
    Schedule::command('backup:run --only-db')->dailyAt('02:00');
    Schedule::command('backup:clean')->quarterly();
}
