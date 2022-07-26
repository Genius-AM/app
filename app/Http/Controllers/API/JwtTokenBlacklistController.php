<?php

namespace App\Http\Controllers\API;

use App\JwtTokenBlacklist;
use App\User;
use App\Http\Controllers\Controller;

class JwtTokenBlacklistController extends Controller
{
    /**
     * Возвращае boolean, по тому, валиден ли токен
     *
     * @param $user_id
     * @param $token
     * @return bool
     */
    static public function getValidToken($user_id, $token)
    {

        $token = JwtTokenBlacklist::where('user_id', $user_id)
            ->where('token', $token)
            ->first();

        if(!empty($token)) return true;
            else return false;
    }

    /**
     * Установка валидного токена
     *
     * @param $user_id
     * @param $token
     * @return bool
     */
    static public function setValidToken($user_id, $token)
    {
        $record = new JwtTokenBlacklist();
        $record->user_id = $user_id;
        $record->token = $token;
        $result = $record->Save();

        return $result;
    }

    /**
     * Удаление всех токенов у пользователя
     *
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    static public function removeTokensByUser(User $user)
    {
        $records = $user->jwt()->get();

        if ($records->count()) {
            /** @var JwtTokenBlacklist $value */
            foreach ($records as $key => $value) {
                $value->delete();
            }

            return true;
        } else return false;
    }
}
