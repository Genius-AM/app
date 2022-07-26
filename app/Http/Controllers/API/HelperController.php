<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Excursion;
use App\Order;
use App\PassengersAmountInExcursion;
use App\Subcategory;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

class HelperController extends Controller
{
    /**
     * Проверка пользователя на действитеоьность его токена
     *
     * @param $manager_id
     * @param $authorization
     * @return bool
     */
    static public function checkValidToken($manager_id, $authorization)
    {
        //проверка на валидность токена
        if($authorization){
            $token = explode('Bearer ', $authorization)[1];
            return JwtTokenBlacklistController::getValidToken($manager_id, $token);
        }

        return false;
    }


    /**
     * Возврат количества занимаемого места, для старой заявки у текущей экскурсии
     *  если таковая есть
     *
     * @param Excursion $excursion
     * @param Order $order
     * @return int
     */
    static public function getOldAmountPeople(Excursion $excursion, Order $order)
    {
        $result = 0;
        if (count($excursion->orders) > 0) {
            foreach ($excursion->orders as $key => $value) {
                if ($value->id == $order->id) {
                    $result = (int) $order->men + (int) $order->women + (int) $order->kids;

                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Срез секунд во времени
     *
     * @param $time
     * @return string
     */
    static public function getTimeWithGoodSeconds($time)
    {
        $result = '';
        if(!empty($time)){
            $timeArray = explode(':', $time);
            $result = $timeArray[0] . ":" . $timeArray[1] . ":00";
        }

        return $result;
    }

    /**
     * @param Excursion $excursion
     * @return array
     */
    static public function amountPeoplesInExcursion(Excursion $excursion)
    {
        if ($excursion->orders->count()) {
            $amount['men'] = $excursion->orders->sum('men');
            $amount['women'] = $excursion->orders->sum('women');
            $amount['kids'] = $excursion->orders->sum('kids');
        }

        return ['men' => $amount['men'] ?? 0, 'women' => $amount['women'] ?? 0, 'kids' => $amount['kids'] ?? 0];
    }

    /**
     * @param $routeId
     * @param $date
     * @param $time
     * @param $subcategoryId
     * @param $categoryId
     * @param null $companyId
     * @return array
     */
    static public function passengersAmountInExcursion($routeId, $date, $time, $subcategoryId, $categoryId, $companyId = null): array
    {
        $men = $women = $kids = null;

        $pass_amount = PassengersAmountInExcursion::where('route_id', $routeId)
            ->where('date', $date)
            ->where('time', $time)
            ->when($categoryId == Category::QUADBIKE && $companyId, function (Builder $query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->first();
        if ($pass_amount) {
            $men = $pass_amount->amount_men;
            $women = $pass_amount->amount_women;
            $kids = $pass_amount->amount_kids;
        }

        $excursion = Excursion::where('route_id', $routeId)
            ->where('date', $date)
            ->where('time', $time)
            ->first();
        if ($excursion) {
            if (!isset($men) && isset($excursion->car->men)) {
                $men = $excursion->car->men;
            }
            if (!isset($women) && isset($excursion->car->women)) {
                $women = $excursion->car->women;
            }
            if (!isset($kids) && isset($excursion->car->kids)) {
                $kids = $excursion->car->kids;
            }
        }

        $subcategory = Subcategory::find($subcategoryId);
        if ($subcategory) {
            if (!isset($men) && isset($subcategory->men)) {
                $men = $subcategory->men;
            }
            if (!isset($women) && isset($subcategory->women)) {
                $women = $subcategory->women;
            }
            if (!isset($kids) && isset($subcategory->kids)) {
                $kids = $subcategory->kids;
            }
        }

        $category = Category::find($categoryId);
        if ($category) {
            if (!isset($men) && isset($category->men)) {
                $men = $category->men;
            }
            if (!isset($women) && isset($category->women)) {
                $women = $category->women;
            }
            if (!isset($kids) && isset($category->kids)) {
                $kids = $category->kids;
            }
        }

        if (!isset($men)) {
            $men = $categoryId != Category::SEA ? 5 : 9;
        }
        if (!isset($women)) {
            $women = $categoryId != Category::SEA ? 5 : 0;
        }
        if (!isset($kids)) {
            $kids = $categoryId != Category::SEA ? 5 : 2;
        }

        return ['men' => $men, 'women' => $women, 'kids' => $kids];

    }

    /**
     * Возвращаем сокращенный вид маршрута
     *  Грозовые Ворота - ГВ
     *
     * @param $text
     * @return string
     */
    static public function getShortRouteName($text)
    {
        if(!empty($text)){
            $result = '';
            $arr_text = explode(' ', $text);
            foreach ($arr_text as $key => $value){
                $result .= mb_substr($value, 0, 1);
            }
            return strtoupper($result);
        } else return '';
    }

    /**
     * Отправка уведомления пользователю
     *
     * @param $user_id
     * @param $title
     * @param $description
     */
    static public function sendPushNotification($user_id, $title, $description)
    {
        $server_key = Config::get('constants.firebase_token');

        $user = User::where('id', $user_id)->with('deviceToken')->first();

        if(count($user->deviceToken) > 0){
            foreach( $user->deviceToken as $key => $value ){
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

                if(empty($value->device_ios) && empty($value->device_android)) continue;
                if($value->device_ios == 1) {
                    $extraNotificationData = ['title'=>$title, "body" => $description];
                    $fcmNotification = [
                        'to'                => $value->token, //device token
                        'notification'      => $extraNotificationData
                    ];
                }
                if ($value->device_android == 1){
                    $extraNotificationData = ['title'=>$title, "message" => $description];
                    $fcmNotification = [
                        'to'                => $value->token, //device token
                        'data'              => $extraNotificationData
                    ];
                }

                $headers = [
                    'Authorization: key='.$server_key,
                    'Content-Type: application/json'
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                $result = curl_exec($ch);
                curl_close($ch);
            }
        }
    }
}
