<?php

namespace App\Http\Controllers\API;

use App\AppVersion;
use App\Core\Device\DeviceOptions;
use App\UsersDeviceToken;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class DeviceController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/Device/SaveDeviceToken",
     *     summary="Сохранение токена устройства",
     *     description="Сохранение токена устройства",
     *     tags={"device"},
     *     @SWG\Parameter(
     *         name="device_id",
     *         in="path",
     *         description="Идентификатор устройства",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="token",
     *         in="path",
     *         description="Токен",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="device_ios",
     *         in="path",
     *         description="Какое устройство, 1 если айос",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Parameter(
     *         name="device_android",
     *         in="path",
     *         description="Какое устройство, 1 если андроид",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Пришли не все данные!",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     */
    /**
     * Сохраняем токен устройства
     *
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function saveDeviceToken(Request $request)
    {
        $device_id = $request->input('device_id');
        $token = $request->input('token');

        if(empty($device_id) || empty($token)) return response(['message'=>'Пришли не все данные!'], 400);

        $device_ios = $request->input('device_ios');
        $device_android = $request->input('device_android');
        if (empty($device_ios) && empty($device_android)) {
            return response(['message'=>'Не выбрана модель устройства'], 400);
        }

        if (!empty($device_ios) && !empty($device_android)) {
            return response(['message'=>'Выбраны оба модели устройства, выберите только одно'], 400);
        }

        $user = $request->user()->with('deviceToken')->first();

        if ($user->deviceToken->count()) {
            //токены найдены
            /** @var UsersDeviceToken $value */
            foreach($user->deviceToken as $key => $value) {
                if ($value->device_id == $device_id) {
                    $value->token = $token;
                    $value->save();

                    return response(['message'=>'Токен обновлен!'], 200);
                }
            }

            //такого устройства нет
            $result = DeviceOptions::saveNewDeviceWithToken($request->user()->id, $device_id, $token, $device_android, $device_ios);
            if($result) return response(['message'=>'Токен сохранен!'], 200);
                else return response(['message'=>'Что-то пошло не так!'], 400);
        } else {
            //токенов нет вообще
            $result = DeviceOptions::saveNewDeviceWithToken($request->user()->id, $device_id, $token, $device_android, $device_ios);

            if($result) return response(['message'=>'Токен сохранен!'], 200);
                else return response(['message'=>'Что-то пошло не так!'], 400);
        }
    }

    /**
     * @SWG\Get(
     *     path="/Device/GetAppVersion",
     *     summary="Возврат версии приложения",
     *     description="Возврат версии приложения",
     *     tags={"device"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="version", type="number", description="Версия приложения"),
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     */
    /*
     * Возврат версии приложения
     */
    public function getAppVersion()
    {
        $version = AppVersion::first();

        return response(['version'=>(int)$version->version], 200);
    }
}
