<?php

namespace App\Http\Middleware\API;

use Closure;

class Driver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->user()->isRole('admin') and !$request->user()->isRole('driver')) {
            return response(['message'=>'Пользователь не имеет доступа по текущему запросу'], 400);
        }
        return $next($request);
    }
}
