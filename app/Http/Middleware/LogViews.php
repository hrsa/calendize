<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LogViews
{
    public function handle(Request $request, Closure $next)
    {
        $ip = request()->ip();
        $routeName = request()->route()->getName();
        Redis::pfAdd('views.' . $routeName, (array) $ip);

        return $next($request);
    }
}
