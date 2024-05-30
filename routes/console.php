<?php

use Illuminate\Support\Facades\Schedule;

if (app()->isProduction()) {
    Schedule::command('backup:run --only-db')->dailyAt('02:00');
    Schedule::command('backup:clean')->quarterly();
}
