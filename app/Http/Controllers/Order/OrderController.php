<?php

namespace App\Http\Controllers\Order;

use App\Category;
use App\Core\Notifications\PushNotification;
use App\Core\Timetable\TimetableOptions;
use App\Excursion;
use App\Http\Controllers\Controller;
use App\Order;
use App\Route;
use App\Subcategory;
use App\Time;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @param Order $order
     * @param Request $request
     * @return JsonResponse
     */
	public function orderChange(Order $order, Request $request)
    {
        $time = Time::findOrFail($request->input('time'));

        if ($order->date != $request->input('date')) {
            if (Carbon::createFromFormat('Y-m-d', $request->input('date')) < Carbon::now()->startOfDay()) {
                return response()->json(['message' => "Не удалось сохранить заявку, время выбрано меньше текущего!"], 500);
            }
        }

        $dateTimeMan = \Illuminate\Support\Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i');

        // Если меняется дата и есть экскурсия
        // Убираем из экскурсии
        if ($order->date != $request->input('date') and $order->excursion->count()) {

            /** @var Excursion $excursion */
            $excursion = $order->excursion->first();
            if ($excursion->isFormed()) {
                if (isset($excursion->car->driver)) {
                    $dateTime = \Illuminate\Support\Carbon::parse($excursion->date.' '.$excursion->time)->format('d-m-Y H:i');
                    PushNotification::to($excursion->car->driver)->send('Изменения в заявке', 'Заявка на' . $dateTime . 'была отредактирована диспетчером');
                }
            }

            $this->deleteFromExcursion($order);
        }

        // Если меняется только время и есть экскурсия
        // Ищим экскурсию, иначе создаем новую
        if ($order->date == $request->input('date') and $order->excursion->count()) {
            if ($order->time != $time->name . ':00') {
                /** @var Excursion $excursion */
                $excursion = $order->excursion->first();
                $dateTime = \Illuminate\Support\Carbon::parse($excursion->date.' '.$excursion->time)->format('d-m-Y H:i');

                $capacity = $excursion->capacity;
                $car = $excursion->car_id;

                $totalPersons = (int) $request->input('men') + (int) $request->input('women') + (int) $request->input('kids');

                $search_exc = Excursion::where('date', $request->input('date'))->where('car_id', $car)->where('time', $time->name . ':00')->where('capacity', $capacity)->first();

                if ($search_exc) {
                    if ($search_exc->capacity == 999) {
                        if ($excursion->isFormed()) {
                            if (isset($excursion->car->driver)) {
                                PushNotification::to($excursion->car->driver)->send('Изменения в заявке', 'Заявка на' . $dateTime . 'была отредактирована диспетчером');
                            }
                        }
                        $this->deleteFromExcursion($order);

                        $search_exc->people += $totalPersons;
                        $search_exc->save();

                        $search_exc->orders()->attach($order->id);

                        $order->status_id = 2;
                        $order->save();
                    } else {
                        $availableSeats = $search_exc->capacity - $search_exc->people;

                        if ($totalPersons <= $availableSeats === false) {
                            // not enough seats available for this order in the current excursion
                            return response()->json(['message' => "Только {$availableSeats} мест осталось. Выберите другую машину или рейс"], 500);
                        }
                        if ($excursion->isFormed()) {
                            if (isset($excursion->car->driver)) {
                                PushNotification::to($excursion->car->driver)->send('Изменения в заявке', 'Заявка на' . $dateTime . 'была отредактирована диспетчером');
                            }
                        }
                        $this->deleteFromExcursion($order);

                        $search_exc->people += $totalPersons;
                        $search_exc->save();

                        $search_exc->orders()->attach($order->id);

                        $order->status_id = 2;
                        $order->save();
                    }
                } else {
                    if ($excursion->isFormed()) {
                        if (isset($excursion->car->driver)) {
                            PushNotification::to($excursion->car->driver)->send('Изменения в заявке', 'Заявка на' . $dateTime . 'была отредактирована диспетчером');
                        }
                    }
                    $this->deleteFromExcursion($order);

                    if ($order->subcategory_id === 1 && $capacity == 8) {
                        $capacity = 8;
                    } elseif ($order->subcategory_id === 7 && $capacity == 30) {
                        $capacity = 30;
                    } else {
                        $capacity = 999;
                    }

                    $excurs_car_timetable = TimetableOptions::excCarTimetable($car, $request->input('route'), $time->name . ':00', $request->input('date'));

                    $excurs = new Excursion();
                    $excurs->exc_car_timetable_id = $excurs_car_timetable ?? null;
                    $excurs->subcategory_id = $order->subcategory_id;
                    $excurs->route_id = $request->input('route');
                    $excurs->car_id = $car;
                    $excurs->capacity = $capacity;
                    $excurs->people = (int) $request->input('men') + (int) $request->input('women') + (int) $request->input('kids');
                    $excurs->date = $request->input('date');
                    $excurs->time = Carbon::createFromTimeString($time->name . ':00')->format('H:i:00');
                    $excurs->status_id = 2;
                    $excurs->save();
                    $excurs->orders()->attach($order->id);

                    $order->status_id = 2;
                    $order->save();
                }
            } else {
                /** @var Excursion $excursion */
                if ($excursion = $order->excursion->first()) {
                    $dateTime = \Illuminate\Support\Carbon::parse($excursion->date.' '.$excursion->time)->format('d-m-Y H:i');
                    $capacity = $excursion->capacity;
                    $totalPersons = (int)$request->input('men') + (int)$request->input('women') + (int)$request->input('kids');
                    $totalPersonInOrder = (int)$order->men + (int)$order->women + (int)$order->kids;

                    if ($capacity == 999) {

                        if ($excursion->isFormed()) {
                            if (isset($excursion->car->driver)) {
                                PushNotification::to($excursion->car->driver)->send('Изменения в заявке', 'Заявка на' . $dateTime . 'была отредактирована диспетчером');
                            }
                        }
                        $excursion->people = (int)$excursion->people - $totalPersonInOrder + $totalPersons;
                        $excursion->save();
                    } else {
                        $availableSeats = $capacity - ((int)$excursion->people - $totalPersonInOrder);

                        if ($totalPersons <= $availableSeats === false) {
                            // not enough seats available for this order in the current excursion
                            return response()->json(['message' => "Только {$availableSeats} мест осталось. Выберите другую машину или рейс"], 500);
                        }

                        if ($excursion->isFormed()) {
                            if (isset($excursion->car->driver)) {
                                PushNotification::to($excursion->car->driver)->send('Изменения в заявке', 'Заявка на' . $dateTime . 'была отредактирована диспетчером');
                            }
                        }
                        $excursion->people = (int)$excursion->people - $totalPersonInOrder + $totalPersons;
                        $excursion->save();
                    }
                }
            }
        }

        if ($order->category_id == Category::DJIPPING) {
            $order->price = $request->input('price');
        }
        $order->date = $request->input('date');
        $order->route_id = $request->input('route');
        $order->time = $time->name . ':00';
        $order->men = $request->input('men');
        $order->women = $request->input('women');
        $order->kids = $request->input('kids');
        $order->prepayment = $request->input('prepayment');
        $order->point_id = $request->input('point');
        $order->address = $request->input('address');
        $order->save();

        $client = $order->client;
        $client->phone = $request->input('phone');
        $client->phone_2 = $request->input('second_phone');
        $client->save();

        // Уведомление Менеджеру
        PushNotification::to($order->manager)->send('Заявка изменена Диспетчером джиппинга', 'Вашу заявку на ' . $dateTimeMan . ' изменили');

        return response()->json(['success' => 'Заявка сохранена!'], 200);
    }

    /**
     * @param Order $order
     */
    public function deleteFromExcursion(Order $order) {
        $order->status_id = 1;
        $totalPersonInOrder = (int)$order->men + (int)$order->women + (int)$order->kids;
        $order->save();
        $excursion = $order->excursion->first();
        $excursion->people = (int)$excursion->people - $totalPersonInOrder;
        $excursion->save();
        $excursion->orders()->detach($order->id);
        if ($excursion->people === 0) {
            $excursion->delete();
        }
    }

    /**
     * @param Order $order
     * @param Request $request
     * @throws Exception
     */
    public function orderCancel(Order $order, Request $request)
    {
        if ($request->filled('order') and $request->filled('exc')) {
            $order = Order::find($request->input('order'));
            $order->status_id = 1;
            $order->refuser_id = $request->user()->id;
            $order->save();
            $excursion = Excursion::find($request->input('exc'));
            $excursion->people = (int)$excursion->people - $order->peopleSum();
            $excursion->save();
            $excursion->orders()->detach($order->id);
            if ($excursion->people === 0) {
                $excursion->delete();
            }
        }

        $dateTime = \Illuminate\Support\Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i');
        $order->status_id = $order->status_id == 4 ? 8 : 5;
        $order->refuser_id = $request->user()->id;

        if (!$request->input('with_excursion')) {
            $order->driver_id = null;
        }

        $order->save();

        PushNotification::to($order->manager)->send('Заявка отказана Диспетчером джиппинга', 'Вашу заявку на '.$dateTime.' отклонили');
    }

    /**
     * @param Order $order
     */
    public function pushNotificationToDriver(Order $order)
    {
        /** @var Excursion $excursion */
        if ($excursion = $order->excursion->first()) {
            $dateTime = \Illuminate\Support\Carbon::parse($excursion->date.' '.$excursion->time)->format('d-m-Y H:i');
            if (isset($excursion->car->driver)) {
                PushNotification::to($excursion->car->driver)->send('Изменения в заявке', 'Заявка на' . $dateTime . 'была отредактирована диспетчером');
            }
        }
    }

    /**
     * @param Order $order
     * @return JsonResponse
     */
	public function orderInfo(Order $order)
    {
        return response()->json(['order' => $order->load('client')]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
	public function getRoutes(Request $request)
    {
		$routes = Route::where('subcategory_id', $request->id)->get();

		return response()->json($routes);
	}
}
