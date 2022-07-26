<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\User;
use App\UsersDeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/Auth/Login",
     *     summary="Авторизация",
     *     description="Авторизация",
     *     tags={"auth"},
     *     @SWG\Parameter(
     *         name="login",
     *         in="path",
     *         description="Логин",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="path",
     *         description="Пароль",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="device_id",
     *         in="path",
     *         description="Идентификатор устройства",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Успех",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="status", type="string", description="Статус"),
     *                 @SWG\Property(property="expires_in", type="integer", description="Возвращает секунды до истечении токена"),
     *                 @SWG\Property(property="user", type="string", description="Имя пользователя"),
     *                 @SWG\Property(property="user_id", type="integer", description="Идентификатор пользователя"),
     *                 @SWG\Property(property="role", type="string", description="Роль"),
     *                 @SWG\Property(property="role_id", type="integer", description="Идентификатор роли"),
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Ошибка",
     *     ),
     * )
     */
    /**
     * Login user and return a token
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function login(Request $request)
    {
        if (empty($request->device_id))
            return response()->json('Отсутствует идентификатор устройства', 400);

        $credentials = $request->only('login', 'password');
        $token = $this->guard()->attempt($credentials);

        if ($token) {
            /** @var User $user */
            $user = User::where('login', $request->login)->with('role')->first();

            if ($user->device_id !== "0" && empty($user->device_id)) {
                //сохранение токена в бд
                \App\Http\Controllers\API\JwtTokenBlacklistController::setValidToken($user->id, $token);
                $user->device_id = empty($request->device_id) ? 0 : $request->device_id;
                $user->save();

                return $this->getResult($user, $token);
            } else {
                if ($user->category_id == Category::SEA && ($user->role_id == 2 || $user->role_id == 3)) {
                    \App\Http\Controllers\API\JwtTokenBlacklistController::setValidToken($user->id, $token);
                    $user->device_id = empty($request->device_id) ? 0 : $request->device_id;
                    $user->save();

                    return $this->getResult($user, $token);
                }

                if ($user->device_id == $request->device_id) {
                    //сохранение токена в бд
                    \App\Http\Controllers\API\JwtTokenBlacklistController::removeTokensByUser($user);
                    \App\Http\Controllers\API\JwtTokenBlacklistController::setValidToken($user->id, $token);

                    return $this->getResult($user, $token);
                } else {
                    return response()
                        ->json(['message' => 'Для данного пользователя уже привязан телефон. Обратитесь к администратору, чтобы привязать новое устройство.'], 400);
                }
            }
        }
        return response()
            ->json(['message' => 'Неверный логин или пароль'], 400);
    }

    /**
     * @SWG\Post(
     *     path="/Auth/Logout",
     *     summary="Выход из приложения",
     *     description="Выход из приложения",
     *     tags={"auth"},
     *     @SWG\Parameter(
     *         name="device_id",
     *         in="path",
     *         description="Идентификатор устройства",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Выход произведен успешно",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="message", type="string", description="Ответное сообщение"),
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Не авторизован",
     *     ),
     * )
     */
    /**
     * Logout User
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $device_id = $request->device_id;
        if (!empty($device_id)) {
            if ($user->deviceToken->count()) {
                foreach ($user->deviceToken as $key => $value) {
                    if ($value->device_id == $device_id) {
                        $update_token = UsersDeviceToken::find($value->id);
                        $update_token->delete();
                    }
                }
            }
        }

        //удаление токена в бд
        \App\Http\Controllers\API\JwtTokenBlacklistController::removeTokensByUser($request->user());

        $this->guard()->logout();
        return response()->json([
            'message' => 'Выход произведен успешно',
        ], 200);
    }

    /**
     * Get authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);
        return response()->json([
            'message' => 'Успех',
            'data' => $user
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/Auth/Refresh",
     *     summary="Обновление токена",
     *     description="Обновление токена",
     *     tags={"auth"},
     *     @SWG\Response(
     *         response=200,
     *         description="Успех",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="status", type="string", description="Статус"),
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Ошибка обновления приложения",
     *     ),
     * )
     */
    /**
     * Refresh JWT token
     */
    public function refresh()
    {
        $token = null;
        try {
            $token = $this->guard()->refresh();
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        if ($token) {
            return response()
                ->json(['message' => 'Успех'], 200)
                ->header('Authorization', $token);
        }
        return response()->json(['message' => 'Ошибка обновления приложения'], 401);
    }

    /**
     * Return auth guard
     */
    private function guard()
    {
        return Auth::guard('api');
    }

    /**
     * @param $user
     * @param $token
     * @return JsonResponse
     */
    private function getResult($user, $token): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'expires_in' => auth('api')->factory()->getTTL() * 60, //return seconds
            'user' => $user->name,
            'user_id' => $user->id,
            'role' => $user->role->name,
            'role_id' => $user->role->id,
        ])->header('Authorization', $token);
    }
}
