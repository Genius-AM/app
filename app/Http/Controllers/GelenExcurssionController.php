<?php

namespace App\Http\Controllers;

use App\Cars;
use App\Core\Timetable\TimetableOptions;
use App\Excursion;
use App\ExcursionOrder;
use App\Models\ExcursionCarTimetable;
use App\Order;
use Carbon\Carbon;
use Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GelenExcurssionController extends Controller {

    /**
     * This last store method
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function new_store_again(Request $request)
    {
        /** @var Cars $car */
        $car = Cars::find($request->input('chosenCarId'));
        $order_category_id = $request->order['category_id'];
        if($car->driver->category_id != $order_category_id){
            return response()->json(['success' => 201, 'message' => "Категории машины и заявки не совпадают"]);
        }

        $chosenCarId = $request->input('chosenCarId');
        $chosenCar = Cars::find($chosenCarId);
        if( empty( $chosenCar->driver ) )
            return response()->json(['success' => 201, 'message' => "Водитель на данную машину не назначен"]);

        $app_in_excur = (int)Config::get('constants.applications_in_excursion');
        $raceNumber = $request->input('chosenRaceNumber');

        $excursion = Excursion::where('date', $request->order['date'])
            ->where('car_id', $chosenCarId)
            ->get();

        $new_orders = [];
        if(count($excursion) > 0){
            //собераем сначало отказные
            foreach($excursion as $ker => $valer){
                if($valer->status_id == 5 or $valer->status_id == 8){
                    $new_orders[] = $valer;
                    unset($excursion[$ker]);
                }
            }
            foreach($excursion as $ker => $valer){
                $new_orders[] = $valer;
            }
        }
        $excursion = $new_orders;

        if( !empty($excursion) && count($excursion) >= (int)$raceNumber){
            $item = $excursion[ (int)$raceNumber - 1 ];

            if( $item['route_id'] !== $request->order['route']['id'] )
                return response()->json(['success' => 201, 'message' => "Неверный маршрут"]);

            $time_first = Carbon::createFromFormat('H:i:s', $item['time'])->format('H:i');
            $time_second = Carbon::createFromFormat('H:i:s', $request->order['time'])->format('H:i');
            if( $time_first !== $time_second )
                return response()->json(['success' => 201, 'message' => "Неверное время"]);
            if( $item['status_id'] !== 2 && $item['status_id'] !== 5 )
                return response()->json(['success' => 201, 'message' => "Заявка сформирована или зарезервирована"]);

            //check for amount applications in one excursion
            $orders = ExcursionOrder::where('excursion_id', $item['id'])->get();
            if( count($orders) >= $app_in_excur )
                return response()->json(['success' => 201, 'message' => "Привышен лимит на количество заявок"]);

            //check for capacity and and empty seats for current excursion
            $availableSeats = $item->capacity - $item->people;
            $totalPersons = $request->order['men'] + $request->order['women'] + $request->order['kids'];
            if ($totalPersons <= $availableSeats === false) {
                // not enough seats available for this order in the current excursion
                return response()->json([
                    'success' => 201,
                    'message' => "Только {$availableSeats} мест осталось. Выберите другую машину или рейс"
                ]);
            }

            $excur_item = Excursion::find($item->id);

            //have enough seats for current order . Go ahead and update people count on people column and add one new record in excursion_order table
            $excur_item->people += $totalPersons;
            $excur_item->save();

            $excur_item->orders()->attach($request->order['id']);

            $order = Order::find($request->order['id']);
            $order->driver_id = $car->driver_id;
            $order->status_id = 2;
            $order->save();
            return response()->json(['success' => 200, 'message' => 'Order assigned successfully to the car']);
        } else {
            //проверка на даты и время
            foreach($excursion as $key => $value){
                if($value->time == $request->order['time']){
                    return response()->json(['success' => 201, 'message' => "На это время уже есть рейс!"]);
                }
            }

            // there is no excurssion for requested car means you can assign / start a new excurssion
            $this->make_new_assign($request);
        }
    }

    /**
     * This last store method for partner car
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function new_store_partner_again(Request $request)
    {
        /** @var Cars $car */
        $car = Cars::find($request->input('chosenCarId'));
        $order_category_id = $request->order['category_id'];
        if($car->category_id != $order_category_id){
            return response()->json(['success' => 201, 'message' => "Категории машины и заявки не совпадают"]);
        }

        $excursion = Excursion::where('date', $request->order['date'])
            ->where('car_id', $request->chosenCarId)
            ->first();

        if( !empty($excursion) ){
            //check for capacity and and empty seats for current excursion
            $totalPersons = $request->order['men'] + $request->order['women'] + $request->order['kids'];
            $excur_item = Excursion::find($excursion->id);

            //have enough seats for current order . Go ahead and update people count on people column and add one new record in excursion_order table
            $excur_item->people += $totalPersons;
            $excur_item->save();

            $excur_item->orders()->attach($request->order['id']);

            $order = Order::find($request->order['id']);
            $order->driver_id = $car->driver_id;
            $order->status_id = 2;
            $order->save();
            return response()->json(['success' => 200, 'message' => 'Order assigned successfully to the partner car']);
        } else {
            // there is no excurssion for requested car means you can assign / start a new excurssion
            $this->make_new_assign($request, 'partner');
        }
    }

    //если пусто, то добавляем новый оффер
    private function make_new_assign(Request $request, $owner = 'our')
    {
        if ($request->order['subcategory_id'] === 1 && $owner === 'our') {
            $capacity = 8;
        } elseif ($request->order['subcategory_id'] === 7 && $owner === 'our') {
            $capacity = 30;
        } else {
            $capacity = 999;
        }

        /** @var Cars $car */
        $car = Cars::findOrFail($request->input('chosenCarId'));

        // ищем в расписании, если есть добавляем
        $excurs_car_timetable = TimetableOptions::excCarTimetable($request->input('chosenCarId'), $request->order['route']['id'], $request->order['time'], $request->order['date']);

        $excurs = new Excursion();
        $excurs->subcategory_id = $request->order['subcategory_id'];
        $excurs->exc_car_timetable_id = $excurs_car_timetable->id ?? null;
        $excurs->route_id = $request->order['route']['id'];
        $excurs->car_id = $request->input('chosenCarId');
        $excurs->capacity = $capacity;
        $excurs->people = $request->order['men'] + $request->order['women'] + $request->order['kids'];
        $excurs->date = $request->order['date'];
        $excurs->time = Carbon::createFromTimeString($request->order['time'])->format('H:i:00');
        $excurs->status_id = 2;
        $excurs->save();
        $excurs->orders()->attach($request->order['id']);

        $order = Order::find($request->order['id']);
        $order->driver_id = $car->driver_id;
        $order->status_id = 2;
        $order->save();
        return response()->json(['success' => 200, 'message' => 'Order assigned successfully to the car']);
    }
}
