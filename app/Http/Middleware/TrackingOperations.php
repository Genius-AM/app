<?php

namespace App\Http\Middleware;

use App\Models\TrackingOperation;
use Carbon\Carbon;
use Closure;
use donatj\UserAgent\UserAgentParser;
use Illuminate\Support\Facades\Auth;

class TrackingOperations
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
        if ($auth = Auth::check()) {
            $this->saveData($request);
        }

        $response = $next($request);

        if (!$auth && Auth::check()) {
            $this->saveData($request);
        }

        return $response;
    }

    /**
     * @param $request
     */
    private function saveData($request): void
    {
        $parser = new UserAgentParser();

        $ua = $parser->parse();

        $model = new TrackingOperation();
        $model->user_id = $request->user()->id;
        $model->ip = $request->ip();
        $model->user_agent = $request->header('User-Agent');
        $model->platform = $ua->platform();
        $model->browser = $ua->browser();
        $model->browser_version = $ua->browserVersion();
        $model->url = $request->fullUrl();
        $model->created_at = Carbon::now();
        $model->save();
    }
}
