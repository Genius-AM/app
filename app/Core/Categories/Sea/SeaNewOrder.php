<?php

namespace App\Core\Categories\Sea;

use App\Cars;
use App\Category;
use App\Client;
use App\Core\Categories\NewOrderMethod;
use App\Core\Notifications\PushNotification;
use App\Core\Notifications\SmscApi;
use App\Core\Phone\Phone;
use App\Core\Timetable\TimetableOptions;
use App\Excursion;
use App\ExcursionOrder;
use App\Http\Controllers\API\HelperController;
use App\Order;
use App\PassengersAmountInExcursion;
use App\Route;
use App\Subcategory;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class SeaNewOrder implements NewOrderMethod
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $payload = [];

    /**
     * @var int
     */
    private $app_in_excur = 0;

    /**
     * @var PassengersAmountInExcursion
     */
    private $pass_amount;

    /**
     * @var Cars
     */
    private $car;

    /**
     * @var User
     */
    private $user;

    public function __construct($array, User $user)
    {
        $this->app_in_excur = (int) Config::get('constants.applications_in_excursion');
        $this->user = $user;
        $this->setData($array);
    }

    /**
     * @param array $data
     * @return mixed|void
     */
    public function setData(array $data)
    {
        if ($data) {
            $this->data = $data;
            $this->data['men'] = $data['men'] ?? 0;
            $this->data['kids'] = $data['kids'] ?? 0;
            $this->data['client_name'] = $data['client_name'] ?? '';
            $this->data['client_comment'] = $data['client_comment'] ?? '';
            $this->data['client_phone'] = Phone::checkOrAddPlusOnNumber($data['client_phone'] ?? '');
            $this->data['client_phone_2'] = Phone::checkOrAddPlusOnNumber($data['client_phone_2'] ?? '');
            $this->data['client_address'] = $data['client_address'] ?? null;
            $this->data['client_food'] = $data['client_food'] ?? 0;
        }

        $this->payload['amount'] = $this->data['men'] + $this->data['kids'];
        $this->payload['old_amount_people'] = 0;

        $date = Carbon::parse($data['DateTime']);

        $this->car = Cars::where('category_id', Category::SEA)
            ->whereHas('timetables', function (Builder $query) use ($date) {
                $query->where('day', '=', strtolower($date->englishDayOfWeek))
                    ->where('time', '=', $date->format('H:i:s'))
                    ->where('route_id', '=', $this->data['route_id'])
                    ->where(function(Builder $query) use ($date) {
                        $query->whereNull('date')
                            ->orWhere('date', '=', $date->format('Y-m-d'));
                    });
            })
            ->with('driver', 'timetables')
            ->first();
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addOrder()
    {
        $route = Route::findOrFail($this->data['route_id']);

        $this->calculatePrice($route);
        $this->setTime();
        $this->setPassengersAmount();

        $excursions = Excursion::where('date', $this->payload['date'])
            ->where('route_id', $route->id)
            ->where('time', $this->payload['time'])
            ->where('car_id', $this->car->id)
            ->get();

        $new_orders = [];
        if (count($excursions) > 0) {
            //собираем сначала отказные
            foreach($excursions as $ker => $valer) {
                if($valer->status_id == 5 or $valer->status_id == 8) {
                    $new_orders[] = $valer;
                    unset($excursions[$ker]);
                }
            }
            foreach($excursions as $ker => $valer) {
                $new_orders[] = $valer;
            }
        }

        $excursions = $new_orders;

        $this->fixTime();

        // ищем сопоставление со временем, если такого нет, создаем новый
        $our_excursion = '';
        if (!empty($excursions)) {
            foreach($excursions as $iiValue){
                if($iiValue['time'] === $this->payload['time_to_check']){
                    $our_excursion = $iiValue;
                }
            }
        }

        //номер рейса,
        //НУЖЕН НОРМАЛЬНЫЙ АЛГОРИТМ
        $raceNumber = 1;

        if (!empty($our_excursion) && !empty($excursions) && count($excursions) >= (int)$raceNumber) {
            $item = $our_excursion;

            if ($item['route_id'] != $route->id) return response(['message'=>'Экскурсия не создана, неверный маршрут'], 400);
            if ($item['time'] !== $this->payload['time_to_check']) return response(['message'=>'Экскурсия не создана, неверное время'], 400);

            //check for amount applications in one excursion
            $orders = ExcursionOrder::where('excursion_id', $item['id'])->get();
            if(count($orders) >= $this->app_in_excur) return response(['message'=>'Экскурсия не создана, привышен лимит на количество заявок'], 400);


            // проверка на конкретное кол-во мест
            $amount = HelperController::amountPeoplesInExcursion($item);
            $pass_amount = HelperController::passengersAmountInExcursion(
                $item->route_id,
                $item->date,
                $item->time,
                $item->subcategory_id,
                Category::SEA,
                $this->user->company_id
            );
            $can_seat_men = $pass_amount['men'] ? $pass_amount['men'] - $amount['men'] : 9 - $amount['men'];
            if ($can_seat_men < $this->data['men']) {
                return response(['message'=>"Заявка не добавлена. На данное время свободных мест для взрослых: {$can_seat_men}"], 400);
            }
            $can_seat_kids = $pass_amount['kids'] ? $pass_amount['kids'] - $amount['kids'] : 2 - $amount['kids'];
            if ($can_seat_kids < $this->data['kids']) {
                return response(['message'=>"Заявка не добавлена. На данное время свободных мест для детей: {$can_seat_kids}"], 400);
            }

            //check for capacity and and empty seats for current excursion
            $capacity = !empty($this->pass_amount) ? $this->pass_amount->getAmount() : $item->capacity;
            $availableSeats = $capacity - $item->people;
            $totalPersons = $this->payload['amount'];
            if ($totalPersons <= $availableSeats === false) {
                // not enough seats available for this order in the current excursion
                return response(['message'=>"Заявка не добавлена. На данное время свободных мест: {$availableSeats}"], 400);
            }

            //редактирование заявки
            $order = new Order();
            $client = new Client();
            $this->editOrderData($order, $client, $route);

            $excur_item = Excursion::findOrFail($item->id);
            $excur_item->people += $totalPersons;
            $excur_item->capacity = $capacity;
            $excur_item->save();

            $excur_item->orders()->attach($order->id);

        } else {
            //проверка на даты и время
            foreach ($excursions as $key => $value) {
                if ($value->time == $this->payload['time']) {
                    return response(['message'=>"Экскурсия не создана, на это время уже есть рейс!"], 400);
                }
            }

            $capacity = !empty($this->pass_amount) ? $this->pass_amount->getAmount() : 11;
            if ($capacity < $this->payload['amount']) {
                return response(['message'=>"Экскурсия не создана, мест меньше чем вы запросили"], 400);
            }

            $pass_amount = HelperController::passengersAmountInExcursion(
                $route->id,
                $this->payload['date'],
                $this->payload['time_to_check'],
                $route->subcategory_id,
                Category::SEA
            );

            if ($pass_amount['men'] < $this->data['men']) {
                return response(['message'=>"Заявка не добавлена. На данное время свободных мест для взрослых: {$pass_amount['men']}"], 400);
            }
            if ($pass_amount['kids'] < $this->data['kids']) {
                return response(['message'=>"Заявка не добавлена. На данное время свободных мест для детей: {$pass_amount['kids']}"], 400);
            }

            //редактирование заявки
            $order = new Order();
            $client = new Client();
            $this->editOrderData($order, $client, $route);

            // ищем в расписании, если есть добавляем
            $excurs_car_timetable = TimetableOptions::excCarTimetable($this->car->id, $route->id, $this->payload['time'], $this->payload['date']);

            //генерация самого рейса
            $excurs = new Excursion();
            $excurs->subcategory_id = $route->subcategory_id;
            $excurs->exc_car_timetable_id = $excurs_car_timetable->id ?? null;
            $excurs->route_id = $route->id;
            $excurs->car_id = $this->car->id;
            // Стандарт 15, изменится возможно
            $excurs->capacity = !empty($this->pass_amount) ? $this->pass_amount->getAmount() : 15;
            $excurs->people = $this->payload['amount'];
            $excurs->date = $this->payload['date'];
            $excurs->time = $this->payload['time'];
            $excurs->status_id = 3;
            $excurs->save();
            $excurs->orders()->attach($order->id);
        }

        //отправка пуша водителю о заявке
        $message = PushNotification::to($this->car->driver)->send('Новая заявка', 'У вас новая заявка на ' . Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i'));

        // Отправка СМС клиенту
        // @TODO: Вынести в контроллер (?)
        SmscApi::to($client)->send('Ваша заявка оформлена! Экскурсия: "' . $route->name . '" ' . Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i') . '. Адрес: ' . $order->address . '. Телефон менеджера: ' . $this->user->phone . '. Телефон диспетчера для экстренных услуг: +7(938)404-17-07');

        return response(['message'=>'Успех'], 200);
    }

    /**
     * @return mixed|void
     */
    public function editOrder()
    {
        /** @var Order $order */
        $order = Order::where('id', $this->data['order_id'])->with('excursion')->first();
        $this->setTime();
        $this->setPassengersAmount();

        $this->fixTime();
        $this->detachExcursionDoNotMatchDataTime($order);

        $route = Route::findOrFail($this->data['route_id']);
        $this->calculatePrice($route);

        /** @var Excursion $excursion */
        $excursion = Excursion::where('date', $this->payload['date'])
            ->where('route_id', $route->id)
            ->where('time', $this->payload['time_to_check'])
            ->where('car_id', $this->car->id)
            ->with('orders')
            ->first();

        if (!empty($excursion)) {

            // проверка на конкретное кол-во мест
            $amount = HelperController::amountPeoplesInExcursion($excursion);
            $pass_amount = HelperController::passengersAmountInExcursion(
                $excursion->route_id,
                $excursion->date,
                $excursion->time,
                $excursion->subcategory_id,
                Category::SEA,
                $this->user->company_id
            );
            $can_seat_men = $pass_amount['men'] ? $pass_amount['men'] : 9 - $amount['men'];
            if ($can_seat_men < $this->data['men']) {
                return response(['message'=>"Заявка не изменена. На данное время свободных мест для взрослых: {$can_seat_men}"], 400);
            }
            $can_seat_kids = $pass_amount['kids'] ? $pass_amount['kids'] : 2 - $amount['kids'];
            if ($can_seat_kids < $this->data['kids']) {
                return response(['message'=>"Заявка не изменена. На данное время свободных мест для детей: {$can_seat_kids}"], 400);
            }

            //редактирование заявки
            $client = Client::find($order->client_id);
            if( $excursion->date != $order->date || $excursion->time != $order->time) {

            } else {
                $this->payload['old_amount_people'] = HelperController::getOldAmountPeople($excursion, $order);
            }

            $this->editOrderData($order, $client, $route);

            if (count($order->excursion) == 0 || $excursion->id != $order->excursion[0]->id) {
                $excursion->people += $this->payload['amount'];
                $excursion->capacity = !empty ($this->pass_amount) ? $this->pass_amount->getAmount() : $excursion->capacity;
                $excursion->save();

                $excursion->orders()->attach($order->id);
            } else if ($this->payload['old_amount_people'] != $this->payload['amount']) {
                $excursion->people = (int)$excursion->people - $this->payload['old_amount_people'] + $this->payload['amount'];
                $excursion->save();
            }
        } else {
            $pass_amount = HelperController::passengersAmountInExcursion(
                $route->id,
                $this->payload['date'],
                $this->payload['time_to_check'],
                $route->subcategory_id,
                Category::SEA
            );


            if ($pass_amount['men'] < $this->data['men']) {
                return response(['message'=>"Заявка не добавлена. На данное время свободных мест для взрослых: {$pass_amount['men']}"], 400);
            }
            if ($pass_amount['kids'] < $this->data['kids']) {
                return response(['message'=>"Заявка не добавлена. На данное время свободных мест для детей: {$pass_amount['kids']}"], 400);
            }


            //редактирование заявки
            $client = Client::find($order->client_id);
            $this->editOrderData($order, $client, $route);

            // ищем в расписании, если есть добавляем
            $excurs_car_timetable = TimetableOptions::excCarTimetable($this->car->id, $route->id, $this->payload['time'], $this->payload['date']);

            //генерация самого рейса
            $excurs = new Excursion();
            $excurs->subcategory_id = $route->subcategory_id;
            $excurs->exc_car_timetable_id = $excurs_car_timetable->id ?? null;
            $excurs->route_id = $route->id;
            $excurs->car_id = $this->car->id;
            $excurs->capacity = !empty($this->pass_amount) ? $this->pass_amount->getAmount() : 15;
            $excurs->people = $this->payload['amount'];
            $excurs->date = $this->payload['date'];
            $excurs->time = $this->payload['time'];
            $excurs->status_id = 3;
            $excurs->save();
            $excurs->orders()->attach($order->id);
        }

        // отправка пуша водителю о заявке
        $message = PushNotification::to($this->car->driver)
            ->send('Заявка отредактирована', 'Заявка на '. Carbon::parse($order->date.' '.$order->time)->format('d-m-Y H:i') .' отредактирована');

        return response(['message'=>'Успех']);
    }

    /**
     * Редактировать \ сохранять заказ
     *
     * @param Order $order
     * @param Client $client
     * @param Route $route
     */
    private function editOrderData(Order $order, Client $client, Route $route)
    {
        $client->name = $this->data['client_name'];
        $client->phone = $this->data['client_phone'];
        $client->phone_2 = $this->data['client_phone_2'];
        $client->comment = $this->data['client_comment'];
        $client->save();

        $order->category_id = $route->subcategory->category_id;
        $order->subcategory_id = $route->subcategory_id;
        $order->route_id = $route->id;
        $order->status_id = 3;
        $order->client_id = $client->id;
        $order->date = $this->payload['date'];
        $order->time = $this->payload['time'];
        $order->manager_id = $this->user->id;
        $order->men = $this->data['men'];
        $order->women = 0;
        $order->kids = $this->data['kids'];
        $order->address = $this->data['client_address'];
        $order->point_id = $this->data['client_point_id'] != null ? $this->data['client_point_id'] : $this->data['client_address_id'];
        $order->price = $this->payload['calculate_price'];
        $order->food = $this->data['client_food'];
        $order->prepayment = $this->data['client_prepayment'];
        $order->driver_id = $this->car->driver->id;
        $order->rent = isset($this->data['rent']) ? $this->data['rent'] : false;
        $order->is_pack = false;
        $order->pack_created = false;
        $order->save();

        $order->age_categories()->detach();
        foreach ($this->data['age_categories'] as $age_category) {
            $order->age_categories()->attach($age_category['id'], ['amount' => $age_category['amount']]);
        }
    }

    /**
     * @param Route $route
     */
    private function calculatePrice(Route $route)
    {
        $this->payload['calculate_price'] = (int) $route->price;
    }

    /**
     *
     */
    private function setTime()
    {
        $this->payload['date'] = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->data['DateTime'])->format('Y-m-d');
        $this->payload['time'] = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->data['DateTime'])->format('H:i:00');
        $this->payload['time_to_check'] = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->data['DateTime'])->format('H:i:00');

    }

    /**
     *
     */
    private function setPassengersAmount()
    {
        /** @var PassengersAmountInExcursion $passengers_amount_from_table */
        $this->pass_amount = PassengersAmountInExcursion::where('route_id', $this->data['route_id'])
            ->where('date', $this->payload['date'])
            ->where('time', Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->data['DateTime'])->format('H:i:00'))
            ->first();
    }

    /**
     * fix time
     */
    private function fixTime()
    {
        $this->payload['time_to_check'] = HelperController::getTimeWithGoodSeconds($this->payload['time']);
    }

    /**
     * Открепить от экскурсий, которые не совпадают по дате или времени
     *
     * @param Order $order
     */
    private function detachExcursionDoNotMatchDataTime(Order $order)
    {
        if (count($order->excursion) > 0) {
            $this->payload['old_amount_people'] = (int) $order->men + (int) $order->kids;
            foreach ($order->excursion as $exKey => $exValue) {
                if ($exValue->date != $this->payload['date'] || $exValue->time != $this->payload['time_to_check']) {
                    $excursion_to_detach = Excursion::find($exValue->id);
                    $excursion_to_detach->people = $excursion_to_detach->people - $this->payload['old_amount_people'];
                    $excursion_to_detach->orders()->detach($order->id);
                    $excursion_to_detach->save();
                }
            }
        }
    }
}
