<?php
namespace App\Http\Middleware;
use App\Http\Controllers\API\HelperController;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
class Authenticate extends Middleware
{
    // Override handle method
    public function handle($request, Closure $next, ...$guards)
    {
        /**
         * Необходимо для тестирования API без токена/устройства.
         *
         * Проверяем наличие ключа API_USER_ID. Если ID задан то принудительно задаём
         * пользователя в $request, в ином случае используем обычную аутентификацию.
         */
        if (env('API_USER_ID')) {
            $request->setUserResolver(function () {
                return \App\User::where('id', env('API_USER_ID'))->first();
            });
        } else {
            if ($this->authenticate($request, $guards) === 'authentication_failed') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if (!HelperController::checkValidToken($request->user()->id, $request->header('Authorization'))) {
                return response(['message' => 'User\'s token invalidate'], 405);
            }
        }

        return $next($request);
    }

    // Override authentication method
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }
        return 'authentication_failed';
    }
}