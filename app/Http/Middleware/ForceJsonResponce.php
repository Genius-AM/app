<?php

namespace App\Http\Middleware;

use Closure;

class ForceJsonResponce
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
        if ($request->header('accept') != 'application/json') {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
