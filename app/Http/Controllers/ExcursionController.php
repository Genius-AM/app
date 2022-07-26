<?php

namespace App\Http\Controllers;

use App\Core\Notifications\PushNotification;
use App\Excursion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class ExcursionController extends Controller
{
    /**
     * Отправка экскурсии
     *
     * @param Request $request
     * @return JsonResponse
     */
	public function send(Request $request)
    {
		$excursion = Excursion::findOrFail($request->input('excursion'));
        if($excursion->status_id == 3){
            $excursion->status_id = 2;
            $excursion->save();
        } else {
            $excursion->load('orders');
            $excursion->load('route.subcategory.category', 'car');

            foreach ($excursion->orders as $order) {
                if($order->status_id != 4){
                    $order->status_id = 3;
                    $order->save();
                }
            }

            //отправка пуш уведомления водителю
            if (isset($excursion->car->driver)) {
                PushNotification::to($excursion->car->driver)->send('У вас новый рейс', 'Вам назначен новый рейс ' . Carbon::parse($excursion->date . ' ' . $excursion->time)->format('d-m-Y H:i'));
            }

            $excursion->status_id = 3;
            $excursion->save();
        }

		return response()->json(200);
	}

    /**
     * Зарезервировать и обратно
     *
     * @param Request $request
     * @return JsonResponse
     */
	public function book(Request $request)
    {
		$excursion = Excursion::findOrFail($request->input('excursion'));

		if($excursion->status_id === 6){
            $excursion->load('orders');

            $excursion->load('route.subcategory.category', 'car');

            foreach ($excursion->orders as $order) {
                $order->status_id = 2;
                $order->save();
            }

            $excursion->status_id = 2;
            $excursion->save();

            return response()->json([200, 'message' => "Экскурсия откреплена"]);
        } else {
            $excursion->load('orders');

            $excursion->load('route.subcategory.category', 'car');

            foreach ($excursion->orders as $order) {
                $order->status_id = 6;
                $order->save();
            }

            $excursion->status_id = 6;
            $excursion->save();

            return response()->json([200, 'message' => "Экскурсия зарезервирована"]);
        }
	}
}