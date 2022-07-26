<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class ClearCache
{
    public function handle($request, Closure $next)
    {
        if (Config::get('app.debug')) {
            Artisan::call('view:clear');
        }

        return $next($request);
    }
}
