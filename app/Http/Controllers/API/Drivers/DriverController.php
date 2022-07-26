<?php

namespace App\Http\Controllers\API\Drivers;

use App\BookedTime;
use App\Cars;
use App\Category;
use App\Core\Booked\BookedOptions;
use App\Core\Categories\ChangeAmountFactory;
use App\Core\Notifications\PushNotification;
use App\Core\Notifications\SmscApi;
use App\Excursion;
use App\Http\Controllers\API\HelperController;
use App\Http\Controllers\GelenBookController;
use App\Http\Requests\API\Drivers\ExcursionCancelRequest;
use App\Http\Requests\ChangeAmountRequest;
use App\Models\ExcursionCarTimetable;
use App\Order;
use App\PassengersAmountInExcursion;
use App\Route;
use App\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class DriverController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/Driver/Excursion/GetActive",
     *     tags={"drivers"},
     *     summary="Получить список активных заказов",
     *     description="Получить список активных заказов",
     *     @SWG\Parameter(
     *         name="subcategory_id",
     *         in="query",
     *         description="Subcategory Id",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="route_id",
     *         in="query",
     *         description="ID маршрута, нужно для квадрациклов",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date",
     *         required=false,
     *         type="string",
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
     *                 @SWG\Property(property="limit", type="integer", description="Вместимость человек"),
     *                 @SWG\Property(
     *                      property="detailed_limit",
     *                      type="object",
     *                      @SWG\Property(property="men", type="integer", description="Подробная вместимость, если есть"),
     *                      @SWG\Property(property="women", type="integer", description="Подробная вместимость, если есть"),
     *                      @SWG\Property(property="kids", type="integer", description="Подробная вместимость, если есть")
     *                 ),
     *                 @SWG\Property(property="people", type="integer", description="Сколько уже есть человек"),
     *                 @SWG\Property(property="status", type="string"),
     *                 @SWG\Property(property="status_id", type="integer"),
     *                 @SWG\Property(property="category", type="string"),
     *                 @SWG\Property(property="category_id", type="integer"),
     *                 @SWG\Property(property="subcategory", type="string"),
     *                 @SWG\Property(property="subcategory_id", type="integer"),
     *                 @SWG\Property(property="route", type="string"),
     *                 @SWG\Property(property="route_id", type="integer"),
     *                 @SWG\Property(property="excursion_short", type="string"),
     *                 @SWG\Property(property="booked", type="integer"),
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Пришли не все данные!",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     */
    /**
     * Получить список активных заказов
     *
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function getActive(Request $request)
    {
        $car = Cars::where('driver_id', $request->user()->id)->first();
        if (empty($car)) return response(['message' => 'За данным водителем машина не закреплена!'], 400);

        $today = Carbon::now()->format('Y-m-d');
        if (!empty($request->subcategory_id) && !empty($request->date)) {
            $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->date)->format('Y-m-d');
            $excursions = Excursion::where('car_id', $car->id)
                ->where('subcategory_id', $request->subcategory_id)
                ->where('date', $date)
                ->whereIn('status_id', [3, 4, 5, 8])
                ->with('route.subcategory.category', 'orders')
                ->get();
        } elseif (!empty($request->subcategory_id)) {
            $excursions = Excursion::where('car_id', $car->id)
                ->where('subcategory_id', $request->subcategory_id)
                ->where('date', '>=', $today)
                ->whereIn('status_id', [3, 4, 5, 8])
                ->with('route.subcategory.category', 'orders')
                ->get();
        } elseif (!empty($request->date)) {
            $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->date)->format('Y-m-d');
            $excursions = Excursion::where('car_id', $car->id)
                ->where('date', $date)
                ->whereIn('status_id', [3, 4, 5, 8])
                ->with('route.subcategory.category', 'orders')
                ->get();
        } else {
            $excursions = Excursion::where('car_id', $car->id)
                ->where('date', '>=', $today)
                ->whereIn('status_id', [3, 4, 5, 8])
                ->with('route.subcategory.category', 'orders')
                ->get();
        }

        $user_data = $request->user();

        $excursions = $this->getFormattedData($excursions, $request->user());

        /** @var Excursion $excursions */

        $new_excursions = [];
        if ($user_data->category_id == Category::DIVING && !empty($request->date)) {
            // дополнение данных
            $weekday = strtolower(Carbon::parse($request->date)->locale('en_En')->isoFormat('dddd'));
            $routes = Route::where('id', 9)->with('days.times')->first();
            $all_times = self::getTimesByRoutes($routes, $weekday);

            if(!empty($all_times)){
                foreach($all_times as $key => $value){
                    $time_array = explode(':', $value->name);
                    $current_time = (int)$time_array[0] < 10 ? '0'.(int)$time_array[0]:$time_array[0];
                    $current_time .= ':';
                    $current_time .= (int)$time_array[1] < 10 ? '0'.(int)$time_array[1]:$time_array[1];
                    $current_time .= ':00';

                    $cur_exc = self::getDataAboutExcursion($current_time, $excursions);
                    $date = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->date)->format('Y-m-d');
                    $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $current_time)->toAtomString();

                    if(!empty($cur_exc)){
                        $new_excursions[] = $cur_exc;
                    } else {
                        /** @var PassengersAmountInExcursion $data */
                        $data = PassengersAmountInExcursion::getDataByParams(9, $date, $current_time);
                        $limit = !empty($data) ? $data->getAmount() : $car->passengers_amount;

                        $category_data = Category::where('id',2)->with('subcategories.routes')->first();



                        $time_arr = explode(':', $current_time);
                        $time = (int)$time_arr[0] . ':' . $time_arr[1];

                        //получение брони на рейс
                        $booked = BookedTime::where('route_id', $category_data->subcategories[0]->routes[0]->id)
                            ->where('date', $date)
                            ->where('time', $time)
                            ->first();

                        $new_excursions[] =
                            [
                                'date'              => $date,
                                'time'              => $current_time,
                                'DateTime'          => $dateTime,
                                'limit'             => $limit,
                                'detailed_limit'    => [
                                    'men'           => null,
                                    'women'           => null,
                                    'kids'           => null,
                                ],
                                'detailed_people'    => [
                                    'men'           => null,
                                    'women'           => null,
                                    'kids'           => null,
                                ],
                                'people'            => 0,
                                'status'            => 0,
                                'status_id'         => 0,
                                'category'          => $category_data->name,
                                'category_id'       => $category_data->id,
                                'subcategory'       => $category_data->subcategories[0]->name,
                                'subcategory_id'    => $category_data->subcategories[0]->id,
                                'route'             => $category_data->subcategories[0]->routes[0]->name,
                                'route_id'          => $category_data->subcategories[0]->routes[0]->id,
                                'excursion_short'   => HelperController::getShortRouteName($category_data->name),
                                'booked'            => empty($booked) ? 0 : $booked->booked
                            ];
                    }
                }
            }
        }
        elseif (($user_data->category_id == Category::QUADBIKE || $user_data->category_id == Category::SEA) && !empty($request->date)) {
//            // новое
            $weekday = strtolower(Carbon::parse($request->date)->locale('en_En')->isoFormat('dddd'));
            $routes = $car->routes;
            /** @var Route $route */
            foreach ($routes as $route) {
                $timetables = $route->timetables()->where('car_id', $car->id)->where('date', Carbon::parse($request->date)->format('Y-m-d'))->get();

                if (!$timetables->count()) {
                    $timetables = $route->timetables()->where('car_id', $car->id)->where('day', $weekday)->where('date', null)->get();
                }

                /** @var ExcursionCarTimetable $timetable */
                foreach ($timetables as $timetable) {
                    $timetableExcursions = $timetable->excursions->where('date', Carbon::parse($request->date)->format('Y-m-d'));

                    if ($timetableExcursions->count()) {
                        $excursions = $this->getFormattedData($timetableExcursions, $request->user());
                        foreach ($excursions as $excursion) {
                            $new_excursions[] = $excursion;
                        }
                    } else {
                        $data = HelperController::passengersAmountInExcursion(
                            $route->id,
                            $date,
                            $timetable->time,
                            $route->subcategory_id,
                            $user_data->category_id,
                            $user_data->company_id
                        );

                        //получение брони на рейс
                        $time_arr = explode(':', Carbon::createFromTimeString($timetable->time)->format('H:i'));
                        $time = (int)$time_arr[0] . ':' . $time_arr[1];

                        $booked = BookedTime::where('route_id', $route->id)
                            ->where('date', $date)
                            ->where('time', $time)
                            ->first();

                        $new_excursions[] =
                            [
                                'date' => $date,
                                'time' => $timetable->time,
                                'DateTime' => Carbon::createFromFormat('Y-m-dH:i', $date . Carbon::createFromTimeString($timetable->time)->format('H:i'))->toAtomString(),
                                'limit' => array_sum($data),
                                'detailed_limit' => [
                                    'men' => $data['men'],
                                    'women' => $data['women'],
                                    'kids' => $data['kids'],
                                ],
                                'detailed_people' => [
                                    'men' => 0,
                                    'women' => 0,
                                    'kids' => 0,
                                ],
                                'people' => 0,
                                'status' => 0,
                                'status_id' => 0,
                                'category' => $route->subcategory->category->name,
                                'category_id' => $route->subcategory->category->id,
                                'subcategory' => $route->subcategory->name,
                                'subcategory_id' => $route->subcategory_id,
                                'route' => $route->name,
                                'route_id' => $route->id,
                                'excursion_short' => HelperController::getShortRouteName($route->name),
                                'booked' => empty($booked) ? 0 : $booked->booked
                            ];
                    }
                }
            }
        }

        return response( !empty($new_excursions) ? $new_excursions : $excursions, 200);
    }

    /**
     * Возврат эскурсии, если она есть
     *
     * @param $current_time
     * @param $excursions
     * @return bool
     */
    private static function getDataAboutExcursion($current_time, $excursions)
    {
        foreach($excursions as $key => $value) {
            if($value['time'] === $current_time) {
                return $value;
            }
        }

        return false;
    }

    /**
     * Возврат id дня по дню недели
     *
     * @param $routes
     * @param $weekday
     * @return array
     */
    static private function getTimesByRoutes($routes, $weekday)
    {
        if(count($routes->days) > 0) {
            foreach($routes->days as $day) {
                if($day->weekday === $weekday) {
                    return $day->times;
                }
            }
        }

        return [];
    }

    /**
     * @SWG\Post(
     *     path="/Driver/Excursion/GetArchive",
     *     tags={"drivers"},
     *     summary="Получить список архивных заказов",
     *     description="Получить список архивных заказов",
     *     @SWG\Parameter(
     *         name="subcategory_id",
     *         in="path",
     *         description="Subcategory Id",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="date",
     *         in="path",
     *         description="Date",
     *         required=false,
     *         type="string",
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
     *                 @SWG\Property(property="limit", type="integer", description="Вместимость человек"),
     *                 @SWG\Property(property="people", type="integer", description="Сколько уже есть человек"),
     *                 @SWG\Property(property="status", type="string"),
     *                 @SWG\Property(property="status_id", type="integer"),
     *                 @SWG\Property(property="category", type="string"),
     *                 @SWG\Property(property="category_id", type="integer"),
     *                 @SWG\Property(property="subcategory", type="string"),
     *                 @SWG\Property(property="subcategory_id", type="integer"),
     *                 @SWG\Property(property="route", type="string"),
     *                 @SWG\Property(property="route_id", type="integer"),
     *                 @SWG\Property(property="excursion_short", type="string"),
     *                 @SWG\Property(property="booked", type="integer"),
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="За данным водителем машина не закреплена!",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     */
    /**
     * Возврат архивных заявок
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function getArchive(Request $request)
    {
        $car = Cars::where('driver_id', $request->user()->id)->first();
        if(empty($car)) return response(['message'=>'За данным водителем машина не закреплена!'], 400);

        $today = Carbon::now()->format('Y-m-d');
        $until= Carbon::now()->subDay(21)->format('Y-m-d');
        if( !empty($request->subcategory_id) && !empty($request->date) ){
            $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->date)->format('Y-m-d');
            $excursions = Excursion::where('car_id', $car->id)
                ->where('subcategory_id', $request->subcategory_id)
                ->where('date', $date)
                ->whereIn('status_id', [3, 4, 5, 8])
                ->with('route.subcategory.category', 'orders')
                ->get();
        } elseif(!empty($request->subcategory_id)){
            $excursions = Excursion::where('car_id', $car->id)
                ->where('subcategory_id', $request->subcategory_id)
                ->where('date', '<', $today)
                ->where('date', '>', $until)
                ->whereIn('status_id', [3, 4, 5, 8])
                ->with('route.subcategory.category', 'orders')
                ->get();
        } elseif(!empty($request->date)){
            $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->date)->format('Y-m-d');
            $excursions = Excursion::where('car_id', $car->id)
                ->where('date', $date)
                ->whereIn('status_id', [3, 4, 5, 8])
                ->with('route.subcategory.category', 'orders')
                ->get();
        } else {
            $excursions = Excursion::where('car_id', $car->id)
                ->where('date', '<', $today)
                ->where('date', '>', $until)
                ->whereIn('status_id', [3, 4, 5, 8])
                ->with('route.subcategory.category', 'orders')
                ->get();
        }
        $excursions = $this->getFormattedData($excursions, $request->user());

        return response($excursions);
    }


    /**
     * @SWG\Post(
     *     path="/Driver/Excursion/Cancel",
     *     tags={"drivers"},
     *     summary="Отменить экскурсию со всеми заказами",
     *     description="Отменить экскурсию со всеми заказами",
     *     @SWG\Parameter(
     *         name="excursion_id",
     *         in="query",
     *         description="Excursion Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="message",
     *         in="query",
     *         description="message",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
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
     *
     * @param ExcursionCancelRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function cancel(ExcursionCancelRequest $request)
    {
        /** @var Excursion $excursion */
        $excursion = Excursion::findOrFail($request->input('excursion_id'));

        foreach ($excursion->orders as $order) {
            // отмена заявок
            $excursion->orders()->detach($order->id);

            if ($order->status_id == 1) {
                $order->status_id = 8; // 8 - отказ после принятия
            } else {
                $order->status_id = 5;
            }

            $order->refuser_id = $request->user()->id;
            $order->save();

            // бронирование
            $date = Carbon::parse($order->date)->format('Y-m-d');
            $time = Carbon::createFromTimeString($order->time)->format('g:i');
            $time_booked = \Carbon\Carbon::parse($order->date.'T'.$order->time)->hour . ":" . explode(":",$time)[1];

            $result = new BookedOptions();
            $result = $result->closeBookData($order->route->subcategory->category_id, $order->route->subcategory_id, $order->route_id, $date, $time_booked);

            // отправка клиенту
            SmscApi::to($order->client)->send($request->input('message'));

            // отправка менеджеру
            PushNotification::to($order->manager)->send('Отмена заявки', $request->input('message'));

            // отправка водителям
            if ($order->driver) {
                PushNotification::to($order->driver)->send('Отмена заявки', $request->input('message'));
            }
        }

        $excursion->people = 0;

        if ($excursion->route->subcategory_id != 2 && $excursion->people === 0) {
            $excursion->delete();
        } else {
            if ($excursion->status_id == 1) {
                $excursion->status_id = 8; // 8 - отказ после принятия
            } else {
                $excursion->status_id = 5;
            }
            $excursion->save();
        }

        return response()->json(['OK']);
    }

    /**
     * @SWG\Get(
     *     path="/Driver/Excursion/Orders/Get",
     *     tags={"drivers"},
     *     summary="Получить данные одного заказа со списком заявок ",
     *     description="Получить данные одного заказа со списком заявок ",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Excursion Id",
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
     *                 @SWG\Property(property="limit", type="integer", description="Вместимость человек"),
     *                 @SWG\Property(property="people", type="integer", description="Сколько уже есть человек"),
     *                 @SWG\Property(property="status", type="string"),
     *                 @SWG\Property(property="status_id", type="integer"),
     *                 @SWG\Property(property="category", type="string"),
     *                 @SWG\Property(property="category_id", type="integer"),
     *                 @SWG\Property(property="subcategory", type="string"),
     *                 @SWG\Property(property="subcategory_id", type="integer"),
     *                 @SWG\Property(property="route", type="string"),
     *                 @SWG\Property(property="route_id", type="integer"),
     *                 @SWG\Property(property="excursion_short", type="string"),
     *                 @SWG\Property(property="booked", type="integer"),
     *                 @SWG\Property(
     *                      property="orders",
     *                      type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="date", type="string"),
     *                          @SWG\Property(property="time", type="string"),
     *                          @SWG\Property(property="DateTime", type="string"),
     *                          @SWG\Property(property="men", type="integer"),
     *                          @SWG\Property(property="women", type="integer"),
     *                          @SWG\Property(property="kids", type="integer"),
     *                          @SWG\Property(property="status", type="string"),
     *                          @SWG\Property(property="status_id", type="integer"),
     *                          @SWG\Property(property="client_id", type="integer"),
     *                          @SWG\Property(property="client_name", type="string"),
     *                          @SWG\Property(property="client_comment", type="string"),
     *                          @SWG\Property(property="client_phone", type="string"),
     *                          @SWG\Property(property="client_phone_2", type="string"),
     *                          @SWG\Property(property="client_address", type="string"),
     *                          @SWG\Property(property="client_point", type="string"),
     *                          @SWG\Property(property="client_prepayment", type="integer"),
     *                          @SWG\Property(property="client_price", type="integer"),
     *                          @SWG\Property(property="client_food", type="integer"),
     *                          @SWG\Property(property="calls_count", type="integer"),
     *                          @SWG\Property(property="last_call", type="string"),
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
     *         response="400",
     *         description="Ошибка",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     */
    /**
     * Возврат заявок экскурсии
     *
     * @param \Illuminate\Http\Request $request
     * @return ResponseFactory|Response
     */
    public function getOrders(Request $request)
    {
        $car = Cars::where('driver_id', $request->user()->id)->first();
        if(empty($car)) return response(['message'=>'За данным водителем машина не закреплена!'], 400);

        $excursion_id = $request->id;
        /** @var Excursion $excursion */
        $excursion = Excursion::where('id', $excursion_id)
            ->with('orders.status', 'route.subcategory.category', 'status')
            ->with(['orders' => function($query){
                $query
                    ->whereNotIn('status_id', [5, 7, 8])
                    ->with('client');
            }])
            ->first();

        //получение брони на рейс
        if ($excursion) {
            $time_arr = explode(':', $excursion->time);
            $time = (int)$time_arr[0] . ':' . $time_arr[1];
            $booked = BookedTime::where('route_id', $excursion->route->id)
                ->where('date', $excursion->date)
                ->where('time', $time)
                ->first();
        }

        $result = [];
        if(!empty( $excursion )){
            $result = [
                'id'                => $excursion->id,
                'date'              => $excursion->date,
                'time'              => $excursion->time,
                'DateTime'          => Carbon::parse($excursion->date.' '.$excursion->time)->toAtomString(),
                'limit'             => $excursion->capacity,
                'detailed_limit'    => HelperController::passengersAmountInExcursion(
                    $excursion->route_id,
                    $excursion->date,
                    $excursion->time,
                    $excursion->route->subcategory->id,
                    $excursion->route->subcategory->category->id,
                    $request->user()->company_id
                ),
                'detailed_people'   => HelperController::amountPeoplesInExcursion($excursion),
                'people'            => $excursion->people,
                'status'            => $excursion->status->name,
                'status_id'         => $excursion->status->id,
                'category'          => $excursion->route->subcategory->category->name,
                'category_id'       => $excursion->route->subcategory->category->id,
                'subcategory'       => $excursion->route->subcategory->name,
                'subcategory_id'    => $excursion->route->subcategory->id,
                'route'             => $excursion->route->name,
                'route_id'          => $excursion->route->id,
                'excursion_short'   => HelperController::getShortRouteName($excursion->route->name),
                'booked'            => empty($booked) ? 0 : $booked->booked
            ];

            if( count($excursion->orders) > 0 ){
                foreach ($excursion->orders as $order){
                    $result['orders'][] = [
                        'id'                => $order->id,
                        'date'              => $order->date,
                        'time'              => $order->time,
                        'DateTime'          => Carbon::parse($order->date.' '.$order->time)->toAtomString(),
                        'men'               => $order->men,
                        'women'             => $order->women,
                        'kids'              => $order->kids,
                        'rent'              => $order->rent,
                        'status'            => $order->status->name,
                        'status_id'         => $order->status->id,
                        'client_id'         => $order->client->id,
                        'client_name'       => $order->client->name,
                        'client_comment'    => $order->client->comment,
                        'client_phone'      => $order->client->phone,
                        'client_phone_2'    => $order->client->phone_2,
                        'client_address'    => $order->address,
                        'client_point'      => $order->point_id ? $order->point()->first()->name : "",
                        'client_prepayment' => $order->prepayment,
                        'client_price'      => $order->price,
                        'client_food'       => $order->food,
                        'calls_count'       => $order->calls_count,
                        'last_call'         => $order->last_call ? $order->last_call->toAtomString() : null
                    ];
                }
            } else{
                $result['orders'] = array();
            }
        } else {
            return response(['message'=>'Экскурсия не существует!'], 204);
        }

        return response($result, 200);
    }


    /**
     * @SWG\Post(
     *     path="/Excursion/Orders/CallRecord",
     *     tags={"drivers"},
     *     summary="Увеличение счетчика звонков по заявке",
     *     description="Увеличение счетчика звонков по заявке по order_id",
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
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function orderCallRecord(Request $request)
    {
        try {
            /** @var Order $order */
            $order = Order::findOrFail($request->input('id'));

            /** Проверяем что пользователь и заказ имеют одинаковую категорию */
            if ($order->category_id !== $request->user()->category_id) {
                return response([
                    'message' => sprintf(
                        'Ошибка: заказ №%s и пользователь №%s имеют разные категории.',
                        $order->id,
                        $request->user()->id
                    )
                ], 400);
            }

            /** Если категорией заказа являются квадроциклы то проставляем статус 4 (исполнен) */
            if ($order->category_id === Category::QUADBIKE) {
                $order->status_id = 4;
            }

            $order->calls_count = $order->calls_count + 1;
            $order->last_call = Carbon::now();
            $order->save();

            return response(['message'=>'Успех'], 200);
        } catch (\Exception $exception) {
            return response(['message'=>'Ошибка при сохранении, что-то пошло не так'], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/Driver/Order/Agree",
     *     tags={"drivers"},
     *     summary="Утверждение заявки",
     *     description="Утверждение заявки по order_id",
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
     *         description="Something went wrong",
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
     * Утверждение заявки
     *
     * @param \Illuminate\Http\Request $request
     * @return ResponseFactory|Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setAgreeOrder(Request $request)
    {
        /** @var Cars $car */
        $car = Cars::where('driver_id', $request->user()->id)->first();
        if(empty($car)) return response(['message'=>'За данным водителем машина не закреплена!'], 400);

        $order_id = $request->id;
        $order = Order::where('id', $order_id)
            ->with('excursion.car.driver', 'status')->first();

        if(count($order->excursion) < 1)
            return response(['message'=>'У данной заявки нет экускурсий'], 400);
        else if(empty($order->excursion[0]->car))
            return response(['message'=>'У родительской экскурсии нет закрепленной машины'], 400);
        else if(empty($order->excursion[0]->car->driver))
            return response(['message'=>'У родительской экскурсии у закрепленной машины, нет закрепленного водителя'], 400);
        else if($order->excursion[0]->car->driver_id != $request->user()->id)
            return response(['message'=>'У родительской экскурсии у закрепленной машины, закреплен другой водитель'], 400);
        if($order->excursion[0]->status_id != 3 && $order->excursion[0]->status_id != 5 && $order->excursion[0]->status_id != 8)
            return response(['message'=>'У экскурсии не верный статус'], 400);
        if($order->status_id != 3)
            return response(['message'=>'У заявки не верный статус'], 400);

        $order_to_change = Order::find($order->id);
        $order_to_change->status_id = 4; //4 - исполнен
        $answer = $order_to_change->save();

        if ($order_to_change->category_id == Category::DJIPPING) {
            SmscApi::to($order_to_change->client)->send('Ваша заявка принята водителем! Госномер авто: '. $car->car_number . ', телефонный номер водителя '. $request->user()->phone . '');
        }

        if($answer){
            return response(['message'=>'Успех'], 200);
        } else {
            return response(['message'=>'Ошибка при сохранении, что-то пошло не так'], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/Driver/Order/Cancel",
     *     tags={"drivers"},
     *     summary="Отмена заявки",
     *     description="Отмена заявки по order_id",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="reason",
     *         in="path",
     *         description="Reason of cancel",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Something went wrong",
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
     * Отклонение заявки
     *
     * @param \Illuminate\Http\Request $request
     * @return ResponseFactory|Response
     * @throws \Exception
     */
    public function setCancelOrder(Request $request)
    {
        $car = Cars::where('driver_id', $request->user()->id)->first();
        if(empty($car)) return response(['message'=>'За данным водителем машина не закреплена!'], 400);

        $order_id = $request->id;
        $order = Order::where('id', $order_id)
            ->with('excursion.car.driver', 'status')->first();

        if(count($order->excursion) < 1)
            return response(['message'=>'У данной заявки нет экускурсий'], 400);
        else if(empty($order->excursion[0]->car))
            return response(['message'=>'У родительской экскурсии нет закрепленной машины'], 400);
        else if(empty($order->excursion[0]->car->driver))
            return response(['message'=>'У родительской экскурсии у закрепленной машины, нет закрепленного водителя'], 400);
        else if($order->excursion[0]->car->driver_id != $request->user()->id)
            return response(['message'=>'У родительской экскурсии у закрепленной машины, закреплен другой водитель'], 400);
        if($order->excursion[0]->status_id != 3 && $order->excursion[0]->status_id != 5 && $order->excursion[0]->status_id != 8)
            return response(['message'=>'У экскурсии не верный статус'], 400);
        if($order->status_id != 3 && $order->status_id != 4)
            return response(['message'=>'У заявки не верный статус'], 400);

        $order_to_change = Order::find($order->id);
        $excursion_to_change = Excursion::find($order->excursion[0]->id);

        if(!empty($order_to_change) && !empty($excursion_to_change)){
                $totalPersonInOrder = $order_to_change->men + $order_to_change->women + $order_to_change->kids;
                $excursion_to_change->people = $excursion_to_change->people - $totalPersonInOrder;
                $excursion_to_change->orders()->detach($order_to_change->id);
                if ($excursion_to_change->subcategory_id != 2 && $excursion_to_change->people === 0) {
                    $excursion_to_change->delete();
                } else {
                    $excursion_to_change->status_id = 5; //5 - отказ
                    $excursion_to_change->save();
                }

            $order_to_change->status_id = 5; //5 - отказ
            $order_to_change->refuser_id = $request->user()->id;
            $order_to_change->reason = $request->reason;
            $order_answer = $order_to_change->save();

            if($order_answer){
                //отправка пуш уведомлений на устройства менеджера
                $title = '';
                switch ($order->category_id) {
                    case Category::DJIPPING:
                        $title = 'Заявка отменена водителем';
                        break;
                    case Category::DIVING:
                        $title = 'Заявка отменена администратором сокровища Геленджика';
                        break;
                    case Category::QUADBIKE:
                        $title = 'Заявка отменена диспетчером квадроциклов';
                        break;
                    case Category::SEA:
                        $title = 'Заявка отменена диспетчером моря';
                        break;
                    default:
                        throw new \Exception("Unknown role");
                }

                PushNotification::to($order->manager)->send($title,'Вашу заявку на ' . Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i') . ' отклонили');

                // отправка водителям
                if ($order->driver) {
                    PushNotification::to($order->driver)->send($title,'Заявку на ' . Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i') . ' отклонили');
                }

                return response(['message'=>'Успех'], 200);
            } else {
                return response(['message'=>'Ошибка при сохранении, что-то пошло не так'], 500);
            }
        } else {
            return response(['message'=>'Ошибка при сохранении, что-то пошло не так'], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/Driver/Order/CancelAfterAccept",
     *     tags={"drivers"},
     *     summary="Отмена заявки после принятия",
     *     description="Отмена заявки после принятия по order_id",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="reason",
     *         in="path",
     *         description="Reason of cancel",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Something went wrong",
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
     * Отклонение заявки после принятия
     *
     * @param \Illuminate\Http\Request $request
     * @return ResponseFactory|Response
     * @throws \Exception
     */
    public function setCancelAfterAcceptOrder(Request $request)
    {
        $car = Cars::where('driver_id', $request->user()->id)->first();
        if(empty($car)) return response(['message'=>'За данным водителем машина не закреплена!'], 400);

        $order = Order::where('id', $request->input('id'))
            ->with('excursion.car.driver', 'status')->first();

        if (count($order->excursion) < 1) {
            return response(['message' => 'У данной заявки нет экскурсий'], 400);
        } else if (empty($order->excursion[0]->car)) {
            return response(['message' => 'У родительской экскурсии нет закрепленной машины'], 400);
        } else if (empty($order->excursion[0]->car->driver)) {
            return response(['message' => 'У родительской экскурсии у закрепленной машины, нет закрепленного водителя'], 400);
        } else if($order->excursion[0]->car->driver_id != $request->user()->id) {
            return response(['message' => 'У родительской экскурсии у закрепленной машины, закреплен другой водитель'], 400);
        }

        if ($order->excursion[0]->status_id != 3 && $order->excursion[0]->status_id != 5 && $order->excursion[0]->status_id != 8) {
            return response(['message' => 'У экскурсии не верный статус'], 400);
        }

        if ($order->status_id != 3 && $order->status_id != 4) {
            return response(['message' => 'У заявки не верный статус'], 400);
        }

        $order_to_change = Order::find($order->id);
        $excursion_to_change = Excursion::find($order->excursion[0]->id);

        if ($order_to_change && $excursion_to_change) {
            $totalPersonInOrder = $order_to_change->men + $order_to_change->women + $order_to_change->kids;
            $excursion_to_change->people = $excursion_to_change->people - $totalPersonInOrder;
            $excursion_to_change->orders()->detach($order_to_change->id);
            if ($excursion_to_change->subcategory_id != 2 && $excursion_to_change->people === 0) {
                $excursion_to_change->delete();
            } else {
                $excursion_to_change->status_id = 8; // 8 - отказ после принятия
                $excursion_to_change->save();
            }

            $order_to_change->status_id = 8; // 8 - отказ после принятия
            $order_to_change->refuser_id = $request->user()->id;
            $order_to_change->reason = $request->reason;
            $order_to_change->save();

            if ($order_to_change) {
                $title = '';
                switch ($order->category_id) {
                    case Category::DJIPPING:
                        $title = 'Заявка отменена водителем';
                        break;
                    case Category::DIVING:
                        $title = 'Заявка отменена администратором сокровища Геленджика';
                        break;
                    case Category::QUADBIKE:
                        $title = 'Заявка отменена диспетчером квадроциклов';
                        break;
                    case Category::SEA:
                        $title = 'Заявка отменена диспетчером моря';
                        break;
                    default:
                        throw new \Exception("Unknown role");
                }

                //отправка пуш уведомлений на устройства менеджера
                PushNotification::to($order->manager)->send($title,'Вашу заявку на ' . Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i') . ' отклонили');

                // отправка водителям
                if ($order->driver) {
                    PushNotification::to($order->driver)->send($title,'Заявку на ' . Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i') . ' отклонили');
                }

                return response(['message'=>'Успех'], 200);
            } else {
                return response(['message'=>'Ошибка при сохранении, что-то пошло не так'], 500);
            }
        } else {
            return response(['message'=>'Ошибка при сохранении, что-то пошло не так'], 500);
        }
    }

    /**
     * @SWG\Post(
     *     path="/Driver/BookedTime/Get",
     *     tags={"drivers"},
     *     summary="Получение всех броней",
     *     description="Получение всех броней по категории и дате",
     *     @SWG\Parameter(
     *         name="category_id",
     *         in="path",
     *         description="Category id",
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
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="category_id", type="integer"),
     *                 @SWG\Property(property="subcategory_id", type="integer"),
     *                 @SWG\Property(property="route_id", type="integer"),
     *                 @SWG\Property(property="route_name", type="string"),
     *                 @SWG\Property(property="day", type="string"),
     *                 @SWG\Property(property="date", type="string"),
     *                 @SWG\Property(property="time_id", type="string"),
     *                 @SWG\Property(property="time", type="string"),
     *                 @SWG\Property(property="booked", type="string", description="0 = открыто, 1 = закрыто"),
     *             )
     *         )
     *     ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Something went wrong",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     */
    /**
     * Возврат всей брони на определенный день
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookedTimesGet(Request $request)
    {
        if(!empty($request->category_id) && !empty($request->DateTime)) {
            $bookedOptions = new BookedOptions();
            $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->DateTime)->format('Y-m-d');
            $all_times = $bookedOptions->getAllTimesDataFromCategory($date, $request->category_id);

            if (is_array($all_times)) {
                $all_times_with_booked = $bookedOptions->combineAllDataWithBookedTimes($all_times);
                $all_times_with_booked = $bookedOptions->getNormalDataToDispatcher($all_times_with_booked);
                return response(!empty($all_times_with_booked)?$all_times_with_booked:[$all_times_with_booked], 200);

            } else return response(['message' => 'Не все данные в бд'], 400);

        } else return response(['message' => 'Данных не достаточно'], 400);
    }

    /**
     * @SWG\Post(
     *     path="/Driver/BookTime/Set",
     *     tags={"drivers"},
     *     summary="Добавляем или изменяем на противоположное значение брони",
     *     description="Добавляем или изменяем на противоположное значение брони",
     *     @SWG\Parameter(
     *         name="category_id",
     *         in="path",
     *         description="Category id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="subcategory_id",
     *         in="path",
     *         description="Subcategory id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="route_id",
     *         in="path",
     *         description="Route id",
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
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Something went wrong",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     */
    /**
     * Добавляем или изменяем на противоположное значение брони
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function changeOrAddBookData(Request $request)
    {
        if( !empty($request->category_id) && !empty($request->subcategory_id) && !empty($request->route_id) && !empty($request->DateTime) ){
            $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->DateTime)->format('Y-m-d');
            $time = Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->DateTime)->format('g:i');

            $time_booked = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sP', $request->DateTime)->hour . ":" . explode(":",$time)[1];

            $result = new BookedOptions();
            $result = $result->changeOrAddBookData($request->category_id, $request->subcategory_id, $request->route_id, $date, $time_booked);

            return response(['message' => $result], 200);
        } else return response(['message' => 'Данных не достаточно'], 400);
    }

    /**
     * @SWG\Post(
     *     path="/Driver/PassengersAmount/Set",
     *     tags={"drivers"},
     *     summary="Установка максимального кол-ва мест  на определенный рейс",
     *     description="Установка максимального кол-ва мест  на определенный рейс",
     *     @SWG\Parameter(
     *         name="route_id",
     *         in="query",
     *         description="Route id",
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
     *         name="amount_men",
     *         in="query",
     *         description="Количество мужчин",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="amount_women",
     *         in="query",
     *         description="Количество мужчин",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="amount_kids",
     *         in="query",
     *         description="Количество мужчин",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Something went wrong",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     */
    /**
     * Добавление или изменений количесвта брони за определенный день и рейс
     *
     * @param ChangeAmountRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function changeOrAddPassengersAmountInExcursion(ChangeAmountRequest $request)
    {
        $route = Route::find($request->input('route_id'));

        $changeMethod = ChangeAmountFactory::getChangeMethod($route->category_id, $request->input());
        $result = $changeMethod->changeAmount();

        return response()->json(['message' => $result ? 'Данные сохранены' : 'Превышено максимальное количество мест'], $result ? 200 : 400);
    }

    /**
     * Приводим в нормальный вид данные
     * для методов getActive & getArchive
     *
     * @param $data
     * @param User|null $user
     * @return array
     */
    private function getFormattedData($data, User $user = null)
    {
        if (!empty($data)) {
            $result = [];

            /** @var Excursion $value */
            foreach ($data as $key => $value) {
                $time_arr = explode(':', $value->time);
                $time = (int)$time_arr[0] . ':' . $time_arr[1];

                //получение брони на рейс
                $booked = BookedTime::where('route_id', $value->route->id)
                    ->where('date', $value->date)
                    ->where('time', $time)
                    ->first();

                $result[] = [
                    'id' => $value->id,
                    'date' => $value->date,
                    'time' => $value->time,
                    'DateTime' => Carbon::parse($value->date . ' ' . $value->time)->toAtomString(),
                    'limit' => $value->capacity,
                    'detailed_limit' => HelperController::passengersAmountInExcursion(
                        $value->route_id,
                        $value->date,
                        $value->time,
                        $value->route->subcategory->id,
                        $value->route->subcategory->category->id,
                        $user->company_id
                    ),
                    'people' => $value->people,
                    'detailed_people' => HelperController::amountPeoplesInExcursion($value),
                    'status' => $value->status->name,
                    'status_id' => $value->status->id,
                    'category' => $value->route->subcategory->category->name,
                    'category_id' => $value->route->subcategory->category->id,
                    'subcategory' => $value->route->subcategory->name,
                    'subcategory_id' => $value->route->subcategory->id,
                    'route' => $value->route->name,
                    'route_id' => $value->route->id,
                    'excursion_short' => HelperController::getShortRouteName($value->route->name),
                    'booked' => empty($booked) ? 0 : $booked->booked
                ];

            }

            return $result;
        } else {
            return [];
        }
    }
}
