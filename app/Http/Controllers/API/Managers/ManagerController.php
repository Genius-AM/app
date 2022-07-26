<?php

namespace App\Http\Controllers\API\Managers;

use App\Cars;
use App\Category;
use App\Client;
use App\Core\Notifications\PushNotification;
use App\Core\Notifications\SmscApi;
use App\Core\Phone\Phone;
use App\Core\Timetable\TimetableOptions;
use App\Excursion;
use App\ExcursionOrder;
use App\Http\Controllers\API\HelperController;
use App\Http\Requests\API\Managers\TimesAndLimitsRequest;
use App\Http\Resources\DateTimeLimits;
use App\Order;
use App\PassengersAmountInExcursion;
use App\Route;
use App\Subcategory;
use App\BookedTime;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class ManagerController
{
    /**
     * @SWG\Post(
     *     path="/Manager/Order/GetActive",
     *     tags={"managers"},
     *     summary="Получение активных заявок",
     *     description="Получить список активных заявок",
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="id", type="integer"),
     *                 @SWG\Property(property="date", type="string"),
     *                 @SWG\Property(property="time", type="string"),
     *                 @SWG\Property(property="DateTime", type="string"),
     *                 @SWG\Property(property="men", type="integer"),
     *                 @SWG\Property(property="women", type="integer"),
     *                 @SWG\Property(property="kids", type="integer"),
     *                 @SWG\Property(property="status", type="string"),
     *                 @SWG\Property(property="status_id", type="integer"),
     *                 @SWG\Property(property="client_id", type="integer"),
     *                 @SWG\Property(property="client_name", type="string"),
     *                 @SWG\Property(property="client_comment", type="string"),
     *                 @SWG\Property(property="client_prepayment", type="integer"),
     *                 @SWG\Property(property="client_price", type="integer"),
     *                 @SWG\Property(property="client_phone", type="string"),
     *                 @SWG\Property(property="client_phone_2", type="string"),
     *                 @SWG\Property(property="client_food", type="integer"),
     *                 @SWG\Property(property="client_address", type="string"),
     *                 @SWG\Property(property="category", type="string"),
     *                 @SWG\Property(property="category_id", type="integer"),
     *                 @SWG\Property(property="subcategory", type="string"),
     *                 @SWG\Property(property="subcategory_id", type="integer"),
     *                 @SWG\Property(property="route", type="string"),
     *                 @SWG\Property(property="route_id", type="integer"),
     *                 @SWG\Property(property="excursion_short", type="string")
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
    /**
     * Возврат активных заявок
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function getActiveOrder(Request $request)
    {
        $user = $request->user();

        $today = Carbon::now()->format('Y-m-d');

        $orders = Order::where('manager_id', $user->id)->with('point')
            ->where(function($query) use ($today){
                /** @var Order $query */
                $query
                    ->where('date', '>', $today)
                    ->OrWhere(function($query) use ($today){
                        /** @var Order $query */
                        $query->where('date', $today)->where('status_id', '!=', '4');
                    });
            })
            ->whereNotIn('status_id', [7])
            ->with('status', 'client', 'route.subcategory.category')
            ->orderBy('date', 'asc')
            ->get();

        $orders = $this->getFormattedData($orders);

        return response($orders, 200);
    }

    /**
     * @SWG\Post(
     *     path="/Manager/Order/GetArchive",
     *     tags={"managers"},
     *     summary="Получение архивных заявок",
     *     description="Получить список архивных заявок",
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="id", type="integer"),
     *                 @SWG\Property(property="date", type="string"),
     *                 @SWG\Property(property="time", type="string"),
     *                 @SWG\Property(property="DateTime", type="string"),
     *                 @SWG\Property(property="men", type="integer"),
     *                 @SWG\Property(property="women", type="integer"),
     *                 @SWG\Property(property="kids", type="integer"),
     *                 @SWG\Property(property="status", type="string"),
     *                 @SWG\Property(property="client_id", type="integer"),
     *                 @SWG\Property(property="client_name", type="string"),
     *                 @SWG\Property(property="client_comment", type="string"),
     *                 @SWG\Property(property="client_prepayment", type="integer"),
     *                 @SWG\Property(property="client_price", type="integer"),
     *                 @SWG\Property(property="client_phone", type="string"),
     *                 @SWG\Property(property="client_phone_2", type="string"),
     *                 @SWG\Property(property="client_food", type="integer"),
     *                 @SWG\Property(property="client_address", type="string"),
     *                 @SWG\Property(property="category", type="string"),
     *                 @SWG\Property(property="category_id", type="integer"),
     *                 @SWG\Property(property="subcategory", type="string"),
     *                 @SWG\Property(property="subcategory_id", type="integer"),
     *                 @SWG\Property(property="route", type="string"),
     *                 @SWG\Property(property="route_id", type="integer"),
     *                 @SWG\Property(property="excursion_short", type="string")
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
    /**
     * Возврат активных заявок
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function getArchiveOrder(Request $request)
    {
        $user = $request->user();
        $today = Carbon::now()->format('Y-m-d');

        $orders = Order::where('manager_id', $user->id)->with('point')
            ->where(function ($query) use ($today) {
                /** @var Order $query */
                $query
                    ->where('date', '<', $today)
                    ->OrWhere(function ($query) use ($today) {
                        /** @var Order $query */
                        $query->where('date', $today)->where('status_id', 4);
                    });
            })
            ->whereNotIn('status_id', [7])
            ->with('status', 'client', 'route.subcategory.category')
            ->orderBy('date', 'desc')
            ->get();
        $orders = $this->getFormattedData($orders);

        return response($orders, 200);
    }

    /**
     * @SWG\Post(
     *     path="/Manager/Order/Add",
     *     tags={"managers"},
     *     summary="Добавление новой заявки",
     *     description="Добавление новой заявки",
     *     @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Category Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="subcategory_id",
     *         in="query",
     *         description="Subcategory Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="route_id",
     *         in="query",
     *         description="Route Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="DateTime",
     *         in="query",
     *         description="DateTime",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="men",
     *         in="query",
     *         description="Men amount",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="women",
     *         in="query",
     *         description="Women amount",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="kids",
     *         in="query",
     *         description="Kids amount",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="client_name",
     *         in="query",
     *         description="Client name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_comment",
     *         in="query",
     *         description="Client comment",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_prepayment",
     *         in="query",
     *         description="Client prepayment",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="price",
     *         in="query",
     *         description="Price for djipping",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_phone",
     *         in="query",
     *         description="Client phone",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_phone_2",
     *         in="query",
     *         description="Client second phone",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_food",
     *         in="query",
     *         description="Has client meal? (1 - yes, 0 - no)",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="client_address_id",
     *         in="query",
     *         description="point id",
     *         required=false,
     *         type="integer",
     *         description="POINT ID"
     *     ),
     *     @SWG\Parameter(
     *         name="client_point_id",
     *         in="query",
     *         description="point id",
     *         required=false,
     *         type="integer",
     *         description="POINT ID"
     *     ),
     *     @SWG\Parameter(
     *         name="client_address",
     *         in="query",
     *         description="ID client address",
     *         required=false,
     *         type="string",
     *         description="Not required for dayving, for another properties are required"
     *     ),
     *     @SWG\Parameter(
     *         name="age_categories",
     *         in="body",
     *         required=true,
     *         type="array",
     *         @SWG\Schema(
     *             @SWG\Items(
     *                 @SWG\Property(property="id", type="integer", description="ID age category"),
     *                 @SWG\Property(property="amount", type="integer", description="amount")
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Not of all required data came",
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
     * Добавление новой заявки
     *
     * @param Request $request
     * @return array
     * @throws GuzzleException
     */
    public function storeNewOrder(Request $request)
    {
        $manager_id = $request->user()->id;
        $category_id = $request->input('category_id');
        $subcategory_id = $request->input('subcategory_id');
        $dateTime = $request->input('DateTime');
        $men = $request->input('men');
        $route_id = $request->input('route_id');
        $women = $request->input('women');
        $kids = $request->input('kids');
        $client_name = $request->input('client_name');
        $client_comment = $request->input('client_comment');
        $client_prepayment = $request->input('client_prepayment');
        $price = $request->input('price');
        $client_phone = Phone::checkOrAddPlusOnNumber($request->input('client_phone'));
        $client_phone_2 = Phone::checkOrAddPlusOnNumber($request->input('client_phone_2'));
        $client_food = $request->input('client_food');
        $client_address = $request->input('client_address');
        $client_point_id = $request->filled('client_point_id') ? $request->input('client_point_id') : $request->input('client_address_id');
        $amount_people = (int) $men + (int) $women + (int) $kids;
        $age_categories = $request->input('age_categories');
        $amount_age_categories = is_array($age_categories) ? (int)array_sum(array_column($age_categories, 'amount')) : 0;
        if($amount_people != $amount_age_categories) return response(['message'=>'Не верно указаны категории возрастов'], 400);

        if(
            !empty($dateTime) && !empty($route_id) && !empty($client_name)
            && isset($client_prepayment) && !empty($client_phone)
            && $amount_people > 0 && !empty($manager_id)
            && !empty($category_id) && !empty($subcategory_id)
            && ($category_id == Category::DIVING || $category_id != Category::DIVING && !empty($client_point_id))
            && (($category_id == Category::DJIPPING && isset($price)) || $category_id != Category::DJIPPING)
        ){
            if(empty($client_address)) $client_address = '';

            $route_data = Route::find($route_id);
            if(empty($route_data)) return response(['message'=>'Маршрут не найден'], 400);

            if ($category_id != Category::DJIPPING) {
                $price = (int)$route_data->price_men * (int)$men
                    + (int)$route_data->price_women * (int)$women
                    + (int)$route_data->price_kids * (int)$kids
                    - (int)$client_prepayment;
            }

            $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $dateTime)->format('Y-m-d');
            $time = Carbon::createFromFormat('Y-m-d\TH:i:sP', $dateTime)->format('H:i:00');
            $time_to_check = Carbon::createFromFormat('Y-m-d\TH:i:sP', $dateTime)->format('H:i:00');

            $chosenCar = null;
            //проверка, не превышает ли количетсво людей в заявке для сокровища Геленджика
            if ($category_id == Category::DIVING) {
                $chosenCar = Cars::where('category_id', Category::DIVING)
                    ->with('driver')
                    ->first();
                //сокровища Геленджика
                if(empty($chosenCar)) return response(['message'=>'Заявка не создана, машины нет'], 400);
                if(empty($chosenCar->driver)) return response(['message'=>'Заявка не создана, водитель на данную машину не назначен'], 400);

                $passengers_amount_from_table = PassengersAmountInExcursion::where('route_id',$request->input('route_id'))
                    ->where('date',$date)
                    ->where('time',$time_to_check)
                    ->first();
                $to_check_capacity = !empty($passengers_amount_from_table)?$passengers_amount_from_table->amount:$chosenCar->passengers_amount;
                if($amount_people > $to_check_capacity) return response(['message'=>"Заявка не создана. Мест меньше, чем вы отправили в запросе"], 400);

                $data = $this->getPassengersAmount($request->input('route_id'), $date, $time);
                if ($data['men'] - $men < 0) return response(['message'=>"Заявка не создана. Мест для мужчин меньше, чем вы отправили в запросе"], 400);
                if ($data['women'] - $women < 0) return response(['message'=>"Заявка не создана. Мест для женщин меньше, чем вы отправили в запросе"], 400);
                if ($data['kids'] - $kids < 0) return response(['message'=>"Заявка не создана. Мест для детей меньше, чем вы отправили в запросе"], 400);
            }

            $amount = $this->getAmount($request->input('route_id'), $date, $time);
            if ($amount - $men - $women - $kids < 0) return response(['message'=>"Заявка не создана. Недостаточно мест"], 400);

            //проверка, можно ли добавлять данную заявку
            $time_booked_to_check = Carbon::createFromFormat('Y-m-d\TH:i:sP', $dateTime)->hour . ":" . explode(":",$time)[1];
            if (!$this->checkForCanAddOrder($request->input('route_id'), $date, $time_booked_to_check)) {
                return response(['message'=>'Данное место забронировано!'], 400);
            }

            //НАЧАЛО проверка на то, что в такой день и время можно сохранять заявку
            $weekday = strtolower(Carbon::createFromFormat('Y-m-d\TH:i:sP', $dateTime)->locale('en_En')->isoFormat('dddd'));
            $full_route_data = Route::where('id', $route_id)
                ->with('days.times')
                ->first();
            if( count($full_route_data->days) > 0 ){
                foreach ($full_route_data->days as $key => $value){
                    if($value->weekday == $weekday) {
                        if( count($value->times) === 0 )
                            return response(['message'=>'Данное время недоступно для добавления'], 400);
                        else {
                            $check_to_save = false;
                            foreach($value->times as $k => $v){
                                if(Carbon::createFromFormat('H:i', $v->name)->format('H:i:00') == $time) $check_to_save = true;
                            }

                            if(!$check_to_save) return response(['message'=>'Данное время недоступно для добавления'], 400);
                        }
                    }
                }
            } else return response(['message'=>'Данное время недоступно для добавления'], 400);
            //КОНЕЦ проверка на то, что в такой день и время можно сохранять заявку

            if ($category_id == Category::DIVING) {
                $app_in_excur = (int) Config::get('constants.applications_in_excursion');
                //номер рейса,
                //НУЖЕН НОРМАЛЬНЫЙ АЛГОРИТМ
                $raceNumber = 1;

                $excursion = Excursion::where('date', $date)
                    ->where('car_id', $chosenCar->id)
                    ->get();

                $new_orders = [];
                if (count($excursion) > 0) {
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

                //fix time
                $time_to_check_array = explode(':', $time_to_check);
                $time_to_check = $time_to_check_array[0] . ":" . $time_to_check_array[1] . ":" . "00";

                //ищем сопоставление со временем, если такого нет, создаем новый
                $our_excursion = '';
                if( !empty($excursion) ){
                    foreach($excursion as $iiKey => $iiValue){
                        if( $iiValue['time'] === $time_to_check ){
                            $our_excursion = $iiValue;
                        }
                    }
                }

                if( !empty($our_excursion) && !empty($excursion) && count($excursion) >= (int)$raceNumber){
                    $item = $our_excursion;

                    if($item['route_id'] != $route_id) return response(['message'=>'Экскурсия не создана, неверный маршрут'], 400);
                    if($item['time'] !== $time_to_check) return response(['message'=>'Экскурсия не создана, неверное время'], 400);

                    //check for amount applications in one excursion
                    $orders = ExcursionOrder::where('excursion_id', $item['id'])->get();
                    if(count($orders) >= $app_in_excur) return response(['message'=>'Экскурсия не создана, привышен лимит на количество заявок'], 400);

                    //check for capacity and and empty seats for current excursion
                    $capacity = !empty($passengers_amount_from_table)?$passengers_amount_from_table->amount:$item->capacity;
                    $availableSeats = $capacity - $item->people;
                    $totalPersons = $amount_people;
                    if ($totalPersons <= $availableSeats === false) {
                        // not enough seats available for this order in the current excursion
                        return response(['message'=>"Заявка не добавлена. На данное время свободных мест: {$availableSeats}"], 400);
                    }

                    //редактирование заявки
                    $order = new Order;
                    $client = new Client;
                    $this->editOrderData($order, $client, $client_name, $client_phone, $client_phone_2, $client_comment,
                        $category_id, $subcategory_id, $route_id, $date, $time, $manager_id, $men,
                        $women, $kids, $client_address, $price, $client_food, $client_prepayment, $client_point_id, $age_categories);

                    $excur_item = Excursion::find($item->id);
                    //have enough seats for current order . Go ahead and update people count on people column and add one new record in excursion_order table
                    $excur_item->people += $totalPersons;
                    $excur_item->capacity = $capacity;
                    $excur_item->save();

                    $excur_item->orders()->attach($order->id);

                    $order = Order::find($order->id);
                    $order->status_id = 3;
                    $order->save();
                } else {
                    //проверка на даты и время
                    foreach($excursion as $key => $value){
                        if($value->time == $time){
                            return response(['message'=>"Экскурсия не создана, на это время уже есть рейс!"], 400);
                        }
                    }

                    $capacity = !empty($passengers_amount_from_table)?$passengers_amount_from_table->amount:$chosenCar->passengers_amount;
                    if ($capacity < $amount_people) {
                        // not enough seats available for this order in the current excursion
                        return response(['message'=>"Экскурсия не создана, мест меньше чем вы запросили"], 400);
                    }

                    //редактирование заявки
                    $order = new Order;
                    $client = new Client;
                    $this->editOrderData($order, $client, $client_name, $client_phone, $client_phone_2, $client_comment,
                        $category_id, $subcategory_id, $route_id, $date, $time, $manager_id, $men,
                        $women, $kids, $client_address, $price, $client_food, $client_prepayment, $client_point_id, $age_categories);

                    // ищем в расписании, если есть добавляем
                    $excurs_car_timetable = TimetableOptions::excCarTimetable($chosenCar->id, $route_id, $time, $date);

                    //генерация самого рейса
                    $excurs = new Excursion();
                    $excurs->subcategory_id = $subcategory_id;
                    $excurs->exc_car_timetable_id = $excurs_car_timetable->id ?? null;
                    $excurs->route_id = $route_id;
                    $excurs->car_id = $chosenCar->id;
                    $excurs->capacity = !empty($passengers_amount_from_table)?$passengers_amount_from_table->amount:$chosenCar->passengers_amount;
                    $excurs->people = $amount_people;
                    $excurs->date = $date;
                    $excurs->time = $time;
                    $excurs->status_id = 3;
                    $excurs->save();
                    $excurs->orders()->attach($order->id);

                    $order = Order::find($order->id);
                    $order->status_id = 3;
                    $order->save();
                }

                //отпрвка пуша водителю о заявке
                $dateTime = Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i');

                PushNotification::to($chosenCar->driver)->send('Новая заявка','У вас новая заявка на '.$dateTime);

                SmscApi::to($client)->send('Ваша заявка оформлена! Экскурсия: "' . $full_route_data->name . '" ' . \Illuminate\Support\Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i') . '. Адрес: ' . $order->address . '. Телефон менеджера: ' . $request->user()->phone . '. Телефон диспетчера для экстренных услуг: +7(938)404-17-07');
            } else {
                //редактирование заявки
                $order = new Order;
                $client = new Client;
                $this->editOrderData($order, $client, $client_name, $client_phone, $client_phone_2, $client_comment,
                    $category_id, $subcategory_id, $route_id, $date, $time, $manager_id, $men,
                    $women, $kids, $client_address, $price, $client_food, $client_prepayment, $client_point_id, $age_categories);

                $order = Order::find($order->id);

                SmscApi::to($client)->send('Ваша заявка оформлена! Экскурсия: "' . $full_route_data->name . '" ' . \Illuminate\Support\Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i') . '. Адрес: ' . $order->address . '. Телефон менеджера: ' . $request->user()->phone . '. Телефон диспетчера для экстренных услуг: +7(938)404-17-07');
            }

            return response(['message'=>'Успех'], 200);
        } else {
            $array = 'Поля';
            if (empty($dateTime)) {
                $array = $array . ' DateTime,';
            }
            if (empty($route_id)) {
                $array = $array . ' route_id,';
            }
            if (empty($client_name)) {
                $array = $array . ' client_name,';
            }
            if (!isset($client_prepayment)) {
                $array = $array . ' client_prepayment,';
            }
            if (empty($client_phone)) {
                $array = $array . ' client_phone,';
            }
            if (!($amount_people > 0)) {
                $array = $array . ' men, women, kids,';
            }
            if (empty($category_id)) {
                $array = $array . ' category_id,';
            }
            if (empty($subcategory_id)) {
                $array = $array . ' subcategory_id, ';
            }
            if ($category_id == Category::DIVING && empty($client_point_id)) {
                $array = $array . ' client_point_id или client_address_id,';
            }
            if ($category_id == Category::DJIPPING && !isset($price)) {
                $array = $array . ' price,';
            }

            $array = $array . 'отсутствуют';

            return response(['message' => $array], 400);
        }
    }

    /**
     * Проверка на возможность сохранения заявки
     *  исходя от брони
     * @param $route_id
     * @param $date
     * @param $time
     * @return bool
     */
    static private function checkForCanAddOrder($route_id, $date, $time)
    {
        $data = BookedTime::where('route_id', $route_id)
            ->where('date', $date)
            ->where('time', $time)
            ->first();

        if(empty($data)) return true;
        else {
            if( $data->booked === 1 ) return false;
            else return true;
        }
    }

    /**
     * @SWG\Post(
     *     path="/Manager/Order/Edit",
     *     tags={"managers"},
     *     summary="Редактирование заявки",
     *     description="Редактирование заявки",
     *     @SWG\Parameter(
     *         name="category_id",
     *         in="path",
     *         description="Category Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="subcategory_id",
     *         in="path",
     *         description="Subcategory Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="route_id",
     *         in="path",
     *         description="Route Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="order_id",
     *         in="path",
     *         description="Editing order id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="DateTime",
     *         in="path",
     *         description="DateTime",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="men",
     *         in="path",
     *         description="Men amount",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="women",
     *         in="path",
     *         description="Women amount",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="kids",
     *         in="path",
     *         description="Kids amount",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="client_name",
     *         in="path",
     *         description="Client name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_comment",
     *         in="path",
     *         description="Client comment",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_prepayment",
     *         in="path",
     *         description="Client prepayment",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="price",
     *         in="query",
     *         description="Price for djipping",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_phone",
     *         in="path",
     *         description="Client phone",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_phone_2",
     *         in="path",
     *         description="Client second phone",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_food",
     *         in="path",
     *         description="Has client meal? (1 - yes, 0 - no)",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="client_address_id",
     *         in="path",
     *         description="point id",
     *         required=false,
     *         type="integer",
     *         description="POINT ID"
     *     ),
     *     @SWG\Parameter(
     *         name="client_point_id",
     *         in="path",
     *         description="point id",
     *         required=false,
     *         type="integer",
     *         description="POINT ID"
     *     ),
     *     @SWG\Parameter(
     *         name="client_address",
     *         in="path",
     *         description="client address",
     *         required=false,
     *         type="integer",
     *         description="Not required for dayving, for another properties are required"
     *     ),
     *     @SWG\Parameter(
     *         name="age_categories",
     *         in="body",
     *         required=true,
     *         type="array",
     *         @SWG\Schema(
     *             @SWG\Items(
     *                 @SWG\Property(property="id", type="integer", description="ID age category"),
     *                 @SWG\Property(property="amount", type="integer", description="amount")
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Not of all required data came",
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
     * Редактирвоание заявки
     *
     * @param Request $request
     * @return array
     */
    public function editOrder(Request $request)
    {
        $order_id = $request->input('order_id');
        if(empty($order_id)) return response(['message' => 'Идентификатор заяки отсутствует'], 400);
        /** @var Order $order */
        $order = Order::where('id', $order_id)->with('excursion')->first();

        $category_id = $request->input('category_id');
        $subcategory_id = $request->input('subcategory_id');
        $manager_id = $request->user()->id;
        $dateTime = $request->input('DateTime');
        $route_id = $request->input('route_id');
        $client_name = $request->input('client_name');
        $client_comment = $request->input('client_comment');
        $client_prepayment = $request->input('client_prepayment');
        $price = $request->input('price');
        $client_phone = Phone::checkOrAddPlusOnNumber($request->input('client_phone'));
        $client_phone_2 = Phone::checkOrAddPlusOnNumber($request->input('client_phone_2'));
        $client_food = $request->input('client_food');
        $client_address = $request->input('client_address');
        $client_point_id = $request->filled('client_point_id') ? $request->input('client_point_id') : $request->input('client_address_id');
        $men = $request->input('men');
        $women = $request->input('women');
        $kids = $request->input('kids');
        $old_amount_people = 0;
        $amount_people = (int) $men + (int) $women + (int) $kids;
        $age_categories = $request->input('age_categories');
        $amount_age_categories = is_array($age_categories) ? (int)array_sum(array_column($age_categories, 'amount')) : 0;
        if($amount_people != $amount_age_categories) return response(['message'=>'Не верно указаны категории возрастов'], 400);

        if(empty($dateTime)) return response(['message'=>'Некоторые поля отсутствуют'], 400);
        $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $dateTime)->format('Y-m-d');
        $time = Carbon::createFromFormat('Y-m-d\TH:i:sP', $dateTime)->format('H:i:00');
        //fix time
        $time_to_check = HelperController::getTimeWithGoodSeconds($time);

        $chosenCar = $excursion = $passengers_amount_from_table = null;
        if($order->category_id == Category::DIVING){
            $chosenCar = Cars::where('category_id', Category::DIVING)
                ->with('driver')
                ->first();
            if(empty($chosenCar)) return response(['message'=>'Заявка к заказу не назначена, так как нету машины'], 400);
            if(empty($chosenCar->driver)) return response(['message'=>'Заявка к заказу не назначена, так как у машины нету водителя'], 400);

            /** @var Excursion $excursion */
            $excursion = Excursion::where('date', $date)
                ->where('time', $time_to_check)
                ->where('car_id', $chosenCar->id)
                ->with('orders')
                ->first();

            $passengers_amount_from_table = PassengersAmountInExcursion::where('route_id', $route_id)
                ->where('date',$date)
                ->where('time',$time_to_check)
                ->first();

            if( !empty($excursion) ) {
                if ($excursion->route_id != $route_id)
                    return response(['message' => 'Заявка к заказу не назначена, так как заявка и рейс имеют разные маршруты'], 400);

                //check for amount applications in one excursion
                $orders = ExcursionOrder::where('excursion_id', $excursion['id'])->get();
                $app_in_excur = (int)\Config::get('constants.applications_in_excursion');
                if( count($orders) >= $app_in_excur )
                    return response(['message'=>'Заявка к заказу не назначена, привышен лимит на количество заявок'], 400);

                $capacity = !empty($passengers_amount_from_table)?$passengers_amount_from_table->amount:$excursion->capacity;

                if($excursion->date != $order->date || $excursion->time != $order->time){
                    //check for capacity and and empty seats for current excursion
                    $availableSeats = $capacity - $excursion->people;
                        if ($amount_people <= $availableSeats === false)
                            return response(['message'=>"Заявка к заказу не назначена. Мест меньше чем вы запросили"], 400);
                } else {
                    //возврат $old_amount_people
                    $old_amount_people = HelperController::getOldAmountPeople($excursion, $order);

                    //check for capacity and and empty seats for current excursion
                    $availableSeats = $capacity - $excursion->people + $old_amount_people;
                    if ($amount_people <= $availableSeats === false)
                        return response(['message'=>"Заявка к заказу не назначена. Мест меньше чем вы запросили"], 400);
                }
            } else {
                $capacity = !empty($passengers_amount_from_table)?$passengers_amount_from_table->amount:$chosenCar->passengers_amount;
                if ($capacity < $amount_people) return response(['message'=>"Экскурсия не создана, мест меньше чем вы запросили"], 400);
            }

            //открепить от экскурсий, которые не совпадают по дате или времени
            if( count($order->excursion) > 0 ){
                $old_amount_people = (int) $order->men + (int) $order->women + (int) $order->kids;
                foreach($order->excursion as $exKey => $exValue){
                    if($exValue->date != $date || $exValue->time != $time_to_check){
                        $excursion_to_detach = Excursion::find($exValue->id);
                        $excursion_to_detach->people = $excursion_to_detach->people - $old_amount_people;
                        $excursion_to_detach->orders()->detach($order->id);
                        $excursion_to_detach->save();
                    }
                }
            }

            $data = $this->getPassengersAmount($request->input('route_id'), $date, $time, $order->id);
            if ($data['men'] - $men < 0) return response(['message'=>"Заявка не создана. Мест для мужчин меньше, чем вы отправили в запросе"], 400);
            if ($data['women'] - $women < 0) return response(['message'=>"Заявка не создана. Мест для женщин меньше, чем вы отправили в запросе"], 400);
            if ($data['kids'] - $kids < 0) return response(['message'=>"Заявка не создана. Мест для детей меньше, чем вы отправили в запросе"], 400);

            if($order->status_id != 1 && $order->status_id != 3 && $order->status_id != 5 && $order->status_id != 8)
                return response(['message' => 'Редактировать заявки можно только со статусом Принят, Отказ, Отказ после принятия и Сформирован'], 400);
        } else {
            if($order->status_id != 1 && $order->status_id != 5 && $order->status_id != 8)
                return response(['message' => 'Редактировать заявки можно только со статусом Принят, Отказ и Отказ после принятия'], 400);
        }

        $amount = $this->getAmount($request->input('route_id'), $date, $time, $order->id);
        if ($amount - $men - $women - $kids < 0) return response(['message'=>"Заявка не создана. Недостаточно мест"], 400);

        if(!empty($route_id) && !empty($client_name) && isset($client_prepayment) && !empty($client_phone)
            && $amount_people > 0 && !empty($manager_id) && !empty($order_id) && !empty($order->client_id) && !empty($category_id)
            && !empty($subcategory_id) && ($category_id == Category::DIVING || $category_id != Category::DIVING && !empty($client_point_id))
            && (($category_id == Category::DJIPPING && isset($price)) || $category_id != Category::DJIPPING)
        ){
            if(empty($client_address)) $client_address = '';

            $route_data = Route::find($route_id);
            if(empty($route_data)) return response(['message'=>'Маршрут не найден'], 400);

            if ($category_id != Category::DJIPPING) {
                $price = (int)$route_data->price_men * (int)$men
                    + (int)$route_data->price_women * (int)$women
                    + (int)$route_data->price_kids * (int)$kids
                    - (int)$client_prepayment;
            }

            //НАЧАЛО проверка на то, что в такой день и время можно сохранять заявку
            $weekday = strtolower(Carbon::createFromFormat('Y-m-d\TH:i:sP', $dateTime)->locale('en_En')->isoFormat('dddd'));
            $full_route_data = Route::where('id', $route_id)
                ->with('days.times')
                ->first();
            if( count($full_route_data->days) > 0 ){
                foreach ($full_route_data->days as $key => $value){
                    if($value->weekday == $weekday) {
                        if( count($value->times) === 0 )
                            return response(['message'=>'Данное время недоступно для обновления'], 400);
                        else {
                            $check_to_save = false;
                            foreach($value->times as $k => $v){
                                if( Carbon::createFromFormat('H:i', $v->name)->format('H:i:00') == $time ) $check_to_save = true;
                            }
                            if(!$check_to_save) return response(['message'=>'Данное время недоступно для обновления'], 400);
                        }
                    }
                }
            } else return response(['message'=>'Данное время недоступно для обновления'], 400);
            //КОНЕЦ проверка на то, что в такой день и время можно сохранять заявку

            //сокровища Геленджика
            if($category_id == Category::DIVING){
                if( !empty($excursion) ){
                    //редактирование заявки
                    $client = Client::find($order->client_id);
                    $this->editOrderData($order, $client, $client_name, $client_phone, $client_phone_2, $client_comment,
                        $category_id, $subcategory_id, $route_id, $date, $time, $manager_id, $men,
                        $women, $kids, $client_address, $price, $client_food, $client_prepayment, $client_point_id, $age_categories);

                    if(count($order->excursion) == 0 || $excursion->id != $order->excursion[0]->id){
                        //have enough seats for current order. Go ahead and update people count on people column and add one new record in excursion_order table
                        $excursion->people += $amount_people;
                        $excursion->capacity = $capacity;
                        $excursion->save();

                        $excursion->orders()->attach($order->id);
                    }else if ($old_amount_people != $amount_people){
                        $excursion->people = (int)$excursion->people - $old_amount_people + $amount_people;
                        $excursion->save();
                    }

                    $order = Order::find($order->id);
                    $order->status_id = 3;
                    $order->save();
                } else {
                    //редактирование заявки
                    $client = Client::find($order->client_id);
                    $this->editOrderData($order, $client, $client_name, $client_phone, $client_phone_2, $client_comment,
                        $category_id, $subcategory_id, $route_id, $date, $time, $manager_id, $men,
                        $women, $kids, $client_address, $price, $client_food, $client_prepayment, $client_point_id, $age_categories);

                    // ищем в расписании, если есть добавляем
                    $excurs_car_timetable = TimetableOptions::excCarTimetable($chosenCar->id, $route_id, $time, $date);

                    //генерация самого рейса
                    $excurs = new Excursion();
                    $excurs->subcategory_id = $subcategory_id;
                    $excurs->exc_car_timetable_id = $excurs_car_timetable->id ?? null;
                    $excurs->route_id = $route_id;
                    $excurs->car_id = $chosenCar->id;
                    $excurs->capacity = !empty($passengers_amount_from_table)?$passengers_amount_from_table->amount:$chosenCar->passengers_amount;
                    $excurs->people = $amount_people;
                    $excurs->date = $date;
                    $excurs->time = $time;
                    $excurs->status_id = 3;
                    $excurs->save();
                    $excurs->orders()->attach($order->id);

                    $order = Order::find($order->id);
                    $order->status_id = 3;
                    $order->save();
                }

                //отпрвка пуша водителю о заявке
                $dateTime = Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i');

                PushNotification::to($chosenCar->driver)->send('Заявка отредактирована','Заявка на '.$dateTime.' отредактирована');
            }
            else
            {
                //редактирование заявки
                $client = Client::find($order->client_id);
                $this->editOrderData($order, $client, $client_name, $client_phone, $client_phone_2, $client_comment,
                    $category_id, $subcategory_id, $route_id, $date, $time, $manager_id, $men,
                    $women, $kids, $client_address, $price, $client_food, $client_prepayment, $client_point_id, $age_categories);
            }

            return response(['message'=>'Успех'], 200);
        } else return response(['message'=>'Некоторые поля отсутствуют'], 400);
    }

    /**
     * Редактировать \ сохранять заказ
     *
     * @param Order $order
     * @param Client $client
     * @param $client_name
     * @param $client_phone
     * @param $client_phone_2
     * @param $client_comment
     * @param $category_id
     * @param $subcategory_id
     * @param $route_id
     * @param $date
     * @param $time
     * @param $manager_id
     * @param $men
     * @param $women
     * @param $kids
     * @param $client_address
     * @param $price
     * @param $client_food
     * @param $client_prepayment
     * @param $client_point_id
     * @param $age_categories
     */
    private function editOrderData(Order $order, Client $client, $client_name, $client_phone, $client_phone_2, $client_comment,
                                          $category_id, $subcategory_id, $route_id, $date, $time, $manager_id, $men,
                                          $women, $kids, $client_address, $price, $client_food, $client_prepayment, $client_point_id, $age_categories)
    {
        $client->name = $client_name;
        $client->phone = $client_phone;
        $client->phone_2 = $client_phone_2;
        $client->comment = $client_comment;
        $client->save();

        $order->category_id = $category_id;
        $order->subcategory_id = $subcategory_id;
        $order->route_id = $route_id;
        $order->status_id = $route_id == 9 ? 3 : 1;
        $order->client_id = $client->id;
        $order->date = $date;
        $order->time = $time;
        $order->manager_id = $manager_id;
        $order->men = (int)$men;
        $order->women = (int)$women;
        $order->kids = (int)$kids;
        $order->address = $client_address;
        $order->point_id = $client_point_id;
        $order->price = $price;
        $order->food = $client_food;
        $order->prepayment = $client_prepayment;
        $order->is_pack = false;
        $order->pack_created = false;
        $order->save();

        $order->age_categories()->detach();
        foreach ($age_categories as $age_category) {
            $order->age_categories()->attach($age_category['id'], ['amount' => $age_category['amount']]);
        }
    }

    /**
     * @SWG\Get(
     *     path="/Manager/Order/Get",
     *     tags={"managers"},
     *     summary="Получить данные одной заявки",
     *     description="Получить данные одной заявки",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="id", type="integer"),
     *                 @SWG\Property(property="date", type="string"),
     *                 @SWG\Property(property="time", type="string"),
     *                 @SWG\Property(property="DateTime", type="string"),
     *                 @SWG\Property(property="men", type="integer"),
     *                 @SWG\Property(property="women", type="integer"),
     *                 @SWG\Property(property="kids", type="integer"),
     *                 @SWG\Property(property="status", type="string"),
     *                 @SWG\Property(property="status_id", type="integer"),
     *                 @SWG\Property(property="client_id", type="integer"),
     *                 @SWG\Property(property="client_name", type="string"),
     *                 @SWG\Property(property="client_comment", type="string"),
     *                 @SWG\Property(property="client_prepayment", type="integer"),
     *                 @SWG\Property(property="client_price", type="integer"),
     *                 @SWG\Property(property="client_phone", type="string"),
     *                 @SWG\Property(property="client_phone_2", type="string"),
     *                 @SWG\Property(property="client_food", type="integer"),
     *                 @SWG\Property(property="client_address", type="string"),
     *                 @SWG\Property(property="client_point", type="string"),
     *                 @SWG\Property(property="client_point_id", type="integer"),
     *                 @SWG\Property(property="category", type="string"),
     *                 @SWG\Property(property="category_id", type="integer"),
     *                 @SWG\Property(property="subcategory", type="string"),
     *                 @SWG\Property(property="subcategory_id", type="integer"),
     *                 @SWG\Property(property="route", type="string"),
     *                 @SWG\Property(property="route_id", type="integer"),
     *                 @SWG\Property(property="excursion_short", type="string"),
     *                 @SWG\Property(
     *                     property="age_categories",
     *                     type="array",
     *                     @SWG\Items(
     *                         type="object",
     *                         @SWG\Property(property="id", type="integer"),
     *                         @SWG\Property(property="amount", type="integer")
     *                     )
     *                 )
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
    /**
     * Получить данные одной заявки
     *
     * @param Request $request
     * @return
     */
    public function getOrder(Request $request)
    {
            /** @var Order $order */
            $order = Order::where('id', $request->input('id'))
                ->with('status', 'client', 'route.subcategory.category', 'point', 'age_categories')
                ->first();

            $result = [
                'id' => $order->id,
                'date' => $order->date,
                'time' => $order->time,
                'DateTime' => Carbon::parse($order->date . ' ' . $order->time)->toAtomString(),
                'men' => $order->men,
                'women' => $order->women,
                'kids' => $order->kids,
                'status' => $order->status->name,
                'status_id' => $order->status->id,
                'client_id' => $order->client->id,
                'client_name' => $order->client->name,
                'client_comment' => $order->client->comment,
                'client_prepayment' => $order->prepayment,
                'client_price' => $order->price,
                'client_phone' => Phone::checkOrAddPlusOnNumber($order->client->phone),
                'client_phone_2' => Phone::checkOrAddPlusOnNumber($order->client->phone_2),
                'client_food' => $order->food,
                'company_id' => $order->company_id,
                'client_address' => $order->address,
                'client_address_id' => $order->point_id,
                'client_point' => $order->point_id ? $order->point()->first()->name : "",
                'client_point_id' => $order->point_id,
                'rent' => $order->rent,
                'category' => $order->route->subcategory->category->name ?? "",
                'category_id' => $order->route->subcategory->category->id ?? null,
                'subcategory' => $order->route->subcategory->name ?? "",
                'subcategory_id' => $order->route->subcategory->id ?? null,
                'route' => $order->route->name ?? "",
                'route_id' => $order->route->id ?? null,
                'excursion_short' => HelperController::getShortRouteName($order->route->name ?? ""),
                'reason' => !empty($order->reason) ? $order->reason : '',
                'age_categories' => $order->age_categories->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'amount' => $item->pivot->amount,
                    ];
                })
            ];
        return response($result, 200);
    }

    /**
     * @SWG\Post(
     *     path="/Manager/Order/Cancel",
     *     tags={"managers"},
     *     summary="Отмена заявки",
     *     description="Отмена заявки по order_id",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Order already canceled",
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
     * Удаление заявки
     *
     * @param Request $request
     * @return array
     */
    public function cancelOrder(Request $request)
    {
        /** @var Order $order */
        $order = Order::where('id',$request->input('id'))->with('excursion')->first();
        if(empty($order)) return response(['message'=>'Заявки не существует'], 400);
        if($order->status_id == 7) return response(['message'=>'Заявка уже удалена'], 400);
        if($request->user()->role_id == 1 && $request->user()->category_id == Category::DJIPPING) return response(['message'=>'Невозможно удалить заявку, недостаточно прав'], 400);

        if($order->category_id == Category::DIVING or $order->category_id == Category::QUADBIKE or $order->category_id == Category::SEA){
            if($order->status_id != 1 && $order->status_id != 3 && $order->status_id != 5 && $order->status_id != 8)
                return response(['message' => 'Удалить заявку можно только со статусом Принят, Отказ, Отказ после принятия и Сформирован'], 400);

            if(count($order->excursion) > 0){
                $excursion= Excursion::find($order->excursion[0]->id);
                $excursion->people = $excursion->people - $order->peopleSum();
                $excursion->orders()->detach($order->id);
                $excursion->save();
            }
        } else {
            if($order->status_id != 1 && $order->status_id != 5 && $order->status_id != 8)
                return response(['message' => 'Удалить заявку можно только со статусом Принят, Отказ после принятия и Отказ'], 400);
        }

        $order->status_id = 7;
        $order->refuser_id = $request->user()->id;
        $order->save();

        return response(['message'=>'Заявка была удалена'], 200);
    }

    /**
     * @SWG\Post(
     *     path="/Manager/Times/Get",
     *     tags={"managers"},
     *     summary="Получить список доступного времени",
     *     description="Получить список доступного времени в определенную дату и subcategory_id",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Subcategory Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="date",
     *         in="path",
     *         description="Date",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="route", type="string"),
     *                 @SWG\Property(property="route_id", type="integer"),
     *                 @SWG\Property(
     *                      property="times",
     *                      type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="name", type="string"),
     *                          @SWG\Property(property="DateTime", type="string"),
     *                      )
     *                 )
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
    /**
     * Получить список доступного времени в определенную дату и типа
     *
     * @param Request $request
     * @return array
     */
    public function getTimesByTypeAndDate(Request $request)
    {
        $subcategory_id = $request->input('id');
        $date = $request->input('date');
        $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $date)->format('Y-m-d');

        $weekday = strtolower(Carbon::parse($date)->locale('en_En')->isoFormat('dddd'));

        $routes = Route::where('subcategory_id', $subcategory_id)->get();

        $data = [];
        foreach($routes as $key => $value){
            $result = Route::where('id', $value->id)
                ->with('days.times')
                ->first();

            $times = $this->getTimesByDayInRoute($result->days, $weekday, $date, $value->id);

            $data[] = [
                'route'     => $value->name,
                'route_id'  => $value->id,
                'times'     => $times,
            ];
        }

        if(!empty($data)){
            foreach($data as $iKey => $iValue){
                if(count($iValue['times']) == 0) unset($data[$iKey]);
            }
        }

        $data = array_values($data);

        return response($data, 200);
    }

    /**
     * @SWG\Get(
     *     path="/Manager/Categories/Get",
     *     tags={"managers"},
     *     summary="Получить категории",
     *     description="Получить категории",
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Category")
     *         ),
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
     * Получить категории
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getCategories(Request $request)
    {
        $categories = Category::activeCategories()->get();

        $categories = $this->getFormattedDataCategories($categories);

        return response($categories, 200);
    }

    /**
     * @SWG\Get(
     *     path="/Manager/Subcategories/Get",
     *     tags={"managers"},
     *     summary="Получить все подкатегории",
     *     description="Получить все подкатегории из категории",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="id", type="integer"),
     *                 @SWG\Property(property="name", type="string"),
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
    /**
     * Получить все подкатегории из категории
     *
     * @param Request $request
     * @return array
     */
    public function getSubcategories(Request $request)
    {
        $subcategories = Subcategory::where('category_id', $request->input('id'))->get();

        return response($this->getFormattedDataCategories($subcategories), 200);
    }

    /**
     * @SWG\Post(
     *     path="/Manager/FreePlaces/Get",
     *     tags={"managers"},
     *     summary="Получить все свободные места",
     *     description="Получить все свободные места",
     *     @SWG\Parameter(
     *         name="category_id",
     *         in="path",
     *         description="Category Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="subcategory_id",
     *         in="path",
     *         description="Subcategory Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="route_id",
     *         in="path",
     *         description="Route Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="DateTime",
     *         in="path",
     *         description="DateTime",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *              type="integer",
     *              @SWG\Property(property="count", type="integer"),
     *         )
     *     )
     * )
     */
    /**
     * Возврат свободных мест
     *
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function getFreePlaces(Request $request)
    {
        $date = Carbon::parse($request->input('DateTime'))->format('Y-m-d');
        $time = Carbon::parse($request->input('DateTime'))->format('H:i:s');
        $time = HelperController::getTimeWithGoodSeconds($time);

        $amount = $this->getAmount($request->input('route_id'), $date, $time);

        return response(['count' => $amount < 0 ? 0 : $amount]);
    }



    /**
     * @SWG\Get(
     *     path="/Manager/Routes/Get",
     *     tags={"managers"},
     *     summary="Получить все машруты",
     *     description="Получить все машруты исходя из подкатегории",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Subcategory Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="id", type="integer"),
     *                 @SWG\Property(property="name", type="string"),
     *                 @SWG\Property(property="price_men", type="string"),
     *                 @SWG\Property(property="price_women", type="string"),
     *                 @SWG\Property(property="price_kids", type="string"),
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
    /**
     * Получить все машруты исходя из подкатегории
     *
     * @param Request $request
     * @return array
     */
    public function getRoutes(Request $request)
    {
        $user = $request->user();
        if($user->role_id != 1 && $user->role_id != 4) return response(['message'=>'Пользователь не имеет доступа по текущему запросу'], 400);

        $routes = Route::where('subcategory_id', $request->input('id'))->get();

        $result = [];
        if (!empty($routes)) {
            foreach($routes as $key => $value){
                $result[] = [
                    'id'            => $value->id,
                    'name'          => $value->name,
                    'price_men'     => $value->price_men,
                    'price_women'   => $value->price_women,
                    'price_kids'    => $value->price_kids,
                ];
            }
        }

        return response($result, 200);
    }

    /**
     * Возвращает перебранный массив времени
     *
     * @param $days
     * @param $weekday
     * @return array
     */
    private function getTimesByDayInRoute($days, $weekday, $date, $route_id)
    {
        if(!empty($days)){
            $today = Carbon::now();
            $today_date = $today->format('Y-m-d');
            $today_time = $today->format('H:i');

            $times = [];
            foreach($days as $key => $value){
                if( $value->weekday === $weekday ){
                    $times = $value->times;
                }
            }

            $result = [];
            if(!empty( $times )){
                foreach($times as $k => $v){
                    $get_time_int = (int)str_replace(':','',$v->name);
                    $today_time_int = (int)str_replace(':','',$today_time);

                    $check_booked_data = BookedTime::where('route_id', $route_id)
                        ->where('date', $date)
                        ->where('time', $v->name)
                        ->first();

                    if(!empty($check_booked_data) && $check_booked_data->booked == 1){
                        continue;
                    } else {
                        if($today_date == $date){
                            if($today_time_int > $get_time_int) {
                                continue;
                            } else {
                                $result[] = [
                                    'id'        => $v->id,
                                    'name'      => $v->name,
                                    'DateTime'  => Carbon::createFromFormat('Y-m-dH:i', $date.$v->name)->toAtomString(),
                                ];
                            }
                        } else {
                            $result[] = [
                                'id'        => $v->id,
                                'name'      => $v->name,
                                'DateTime'  => Carbon::createFromFormat('Y-m-dH:i', $date.$v->name)->toAtomString(),
                            ];
                        }
                    }
                }
            }

            return $result;

        } return [];
    }

    /**
     * Приводим в нормальный вид данные
     * для методов -getActiveOrder & getArchiveOrder
     *
     * @param $data
     * @return array
     */
    private function getFormattedData($data)
    {
        if (! empty($data)) {
            $result = [];

                /** @var Order $value */
                foreach ($data as $key => $value) {
                    $result[] = [
                        'id' => $value->id,
                        'date' => $value->date,
                        'time' => $value->time,
                        'DateTime' => Carbon::parse($value->date . ' ' . $value->time)->toAtomString(),
                        'men' => $value->men,
                        'women' => $value->women,
                        'kids' => $value->kids,
                        'status' => $value->status->name,
                        'status_id' => $value->status->id,
                        'client_id' => $value->client->id,
                        'client_name' => $value->client->name,
                        'client_comment' => $value->client->comment,
                        'client_prepayment' => $value->prepayment,
                        'client_price' => $value->price,
                        'client_phone' => Phone::checkOrAddPlusOnNumber($value->client->phone),
                        'client_phone_2' => Phone::checkOrAddPlusOnNumber($value->client->phone_2),
                        'client_food' => $value->food,
                        'client_address' => $value->address,
                        'client_point' => $value->point_id ? $value->point()->first()->name : "",
                        'company_id' => $value->company_id,
                        'company' => !(empty($value->company_id)) ? $value->company->name : "",
                        'category' => !(empty($value->route)) ? $value->route->subcategory->category->name : '',
                        'category_id' => !(empty($value->route)) ? $value->route->subcategory->category->id : null,
                        'subcategory' => !(empty($value->route)) ? $value->route->subcategory->name : '',
                        'subcategory_id' => !(empty($value->route)) ? $value->route->subcategory->id : null,
                        'route' => !(empty($value->route)) ? $value->route->name : '',
                        'route_id' => !(empty($value->route)) ? $value->route->id : null,
                        'excursion_short' => !(empty($value->route)) ? HelperController::getShortRouteName($value->route->name) : '',
                        'reason' => !empty($value->reason) ? $value->reason : '',
                    ];
                }

            return $result;
        } else {
            return [];
        }
    }

    /**
     * Форматируем ответ от методов
     *  getSubcategories & getRoutes & getCategories
     *
     * @param $data
     * @return array
     */
    private function getFormattedDataCategories($data)
    {
        if(!empty($data)){
            $result = [];
            foreach($data as $key => $value){
                $result[] = [
                    'id'    => $value->id,
                    'name'  => $value->name
                ];
            }
            return $result;
        } else return [];
    }


    /**
     * @SWG\Get(
     *     path="/Manager/TimesAndLimits",
     *     tags={"managers"},
     *     summary="Получить свободные места",
     *     description="Получить свободные места",
     *     @SWG\Parameter(
     *         name="route_id",
     *         in="path",
     *         description="Route Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="DateTime",
     *         in="path",
     *         description="DateTime",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="men", type="number", description="men limit"),
     *              @SWG\Property(property="women", type="number", description="women limit"),
     *              @SWG\Property(property="kids", type="number", description="kids limit"),
     *         )
     *     )
     * )
     */
    /**
     * Возврат свободных мест
     *
     * @param TimesAndLimitsRequest $request
     * @return DateTimeLimits
     */
    public function timesAndLimits(TimesAndLimitsRequest $request)
    {
        $date = Carbon::parse($request->input('DateTime'))->format('Y-m-d');
        $time = Carbon::parse($request->input('DateTime'))->format('H:i:s');
        $time = HelperController::getTimeWithGoodSeconds($time);

        $data = $this->getPassengersAmount($request->input('route_id'), $date, $time);

        return new DateTimeLimits([
            'men' => $data['men'] < 0 ? 0 : $data['men'],
            'women' => $data['women'] < 0 ? 0 : $data['women'],
            'kids' => $data['kids'] < 0 ? 0 : $data['kids'],
        ]);
    }

    /**
     * @param $routeId
     * @param $date
     * @param $time
     * @param null $orderId
     * @return array
     */
    private function getPassengersAmount($routeId, $date, $time, $orderId = null): array
    {
        /** @var Route $route */
        $route = Route::where('id', $routeId)
            ->with(['orders' => function($query) use($date, $time){
                /** @var Excursion $query */
                $query->where('date', $date)
                    ->where('time', $time);
            }])
            ->first();

        $data = HelperController::passengersAmountInExcursion(
            $route->id,
            $date,
            $time,
            $route->subcategory_id,
            $route->category_id
        );

        if(!empty($route->orders)){
            foreach($route->orders as $order) {
                if ($order->id !== $orderId) {
                    $data['men'] = $data['men'] - $order->men;
                    $data['women'] = $data['women'] - $order->women;
                    $data['kids'] = $data['kids'] - $order->kids;
                }
            }
        }

        return $data;
    }

    /**
     * @param $routeId
     * @param $date
     * @param $time
     * @param null $orderId
     * @return int|null
     */
    public function getTimetableAmount($routeId, $date, $time): ?int
    {
        if (strlen($time) < 7) {
            $time .= ':00';
        }

        $route = Route::where('id', $routeId)
            ->with([
                'days' => function ($query) use ($date, $time) {
                    $query->where('weekday', '=', strtolower(Carbon::createFromFormat('Y-m-d', $date)->locale('en_En')->isoFormat('dddd')))
                        ->with(['times' => function ($query) use ($time) {
                            $query->where('name', '=', ltrim(Carbon::createFromFormat('H:i:s', $time)->format('H:i'), '0'));
                        }]);
                },
                'route_timetables' => function ($query) use ($date, $time) {
                    $query->where('date', '=', $date)
                        ->where('time', '=', $time);
                },
            ])
            ->first();

        if (
            $route->route_timetables &&
            count($route->route_timetables)
        ) {
            return $route->route_timetables[0]->amount;
        }

        if (
            $route->days &&
            count($route->days) &&
            $route->days[0]->times &&
            count($route->days[0]->times)
        ) {
            return $route->days[0]->times[0]->pivot->amount ?? null;
        }

        return null;
    }

    /**
     * @param $routeId
     * @param $date
     * @param $time
     * @param null $orderId
     * @param bool $freePlaces
     * @return int
     */
    public function getAmount($routeId, $date, $time, $orderId = null, $freePlaces = true): int
    {
        if (strlen($time) < 7) {
            $time .= ':00';
        }

        /** @var Route $route */
        $route = Route::where('id', $routeId)
            ->with(['orders' => function($query) use($date, $time){
                /** @var Excursion $query */
                $query->where('date', $date)
                    ->where('time', $time);
            }])
            ->first();

        $amount = $this->getTimetableAmount($routeId, $date, $time);

        if (!$amount) {
            $data = HelperController::passengersAmountInExcursion(
                $route->id,
                $date,
                $time,
                $route->subcategory_id,
                $route->category_id
            );

            $amount = array_sum($data);
        }

        if($freePlaces && !empty($route->orders)){
            foreach($route->orders as $order) {
                if ($order->id !== $orderId) {
                    $amount = $amount - ($order->men + $order->women + $order->kids);
                }
            }
        }

        return $amount;
    }
}