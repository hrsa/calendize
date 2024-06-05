<?php

namespace App\Console\Commands;

use App\Models\PageView;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

class SyncPageViewsCommand extends Command
{
    protected $signature = 'sync:page-views';

    protected $description = 'Sync page views from Redis to the database';

    public function handle(): void
    {
        $routeNames = $this->getObservedRoutes();

        foreach ($routeNames as $routeName) {
            PageView::updateOrCreate([
                'page'  => $routeName,
                'date'  => today(),
                'views' => Redis::pfCount('views.' . $routeName),
            ]);

            Redis::del('views.' . $routeName);
        }

    }

    private function getObservedRoutes(): array
    {
        $routes = Route::getRoutes();
        $result = [];

        /** @var array $routes */
        foreach ($routes as $route) {
            if (in_array('log-views', $route->gatherMiddleware())) {
                $result[] = $route->getName();
            }
        }

        return $result;
    }
}
