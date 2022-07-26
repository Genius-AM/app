<?php


namespace App\Core\Device;
use App\Cars;
use App\Excursion;
use App\Route;
use App\UsersDeviceToken;

class DeviceOptions
{
    /**
     * Добавление новго устройства и его токена
     *
     * @param $user_id
     * @param $device_id
     * @param $device_android
     * @param $device_ios
     * @param $token
     * @return bool
     */
    static public function saveNewDeviceWithToken($user_id, $device_id, $token, $device_android, $device_ios)
    {
        try {
            //токенов нет вообще
            $new_token = new UsersDeviceToken();
            $new_token->user_id = $user_id;
            $new_token->device_id = $device_id;
            $new_token->token = $token;
            $new_token->device_android = !empty($device_android) ? 1 : 0;
            $new_token->device_ios = !empty($device_ios) ? 1 : 0;
            $result = $new_token->save();
        } catch (\Exception $exception) {
            $result = null;
        }

        return $result;
    }
}