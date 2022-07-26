<?php


namespace App\Core\Route;
use App\Cars;
use App\Category;
use App\Excursion;
use App\Models\ExcursionCarTimetable;
use App\Order;
use App\Route;

class RouteOptions
{
    /**
     * Вернуть количество свободных мест по маршруту, дате и времени
     *
     * @param $route_id
     * @param $date
     * @param $time
     * @return int
     */
    static public function getCountOfFreePlacesByRoute($route_id, $date, $time)
    {
        $partner_cars = Cars::where('owner','partner')->get('id');
        $data_route = Route::where('id', $route_id)
            ->with(['subcategory.category.cars'=>function($query){
                /** @var Cars $query */
                $query->where('owner', 'our');
            }])
            ->with(['subcategory.excursions'=>function($query) use($date, $time, $partner_cars){
                /** @var Excursion $query */
                $query->where('date', $date)
                    ->where('time', $time)
                    ->whereNotIn('car_id', $partner_cars);
            }])
            ->first();

        //passengers_amount
        $place_amount_of_transport = 0;
        if(!empty($data_route->subcategory->category->cars)){
            foreach($data_route->subcategory->category->cars as $key => $value){
                $place_amount_of_transport += (int)$value->passengers_amount;
            }
        }

        //busy_seat_place
        $busy_seat_place = 0;
        if(!empty($data_route->subcategory->excursions)){
            foreach($data_route->subcategory->excursions as $key => $value){
                $busy_seat_place += (int)$value->people;
            }
        }

        return $place_amount_of_transport-$busy_seat_place;
    }

    /**
     * Вернуть количество свободных мест по маршруту, дате и времени относительно всего мест
     *
     * @param $route_id
     * @param $date
     * @param $time
     * @param $amount
     * @return int
     */
    static public function getCountOfFreePlacesByRouteWithAmount($route_id, $date, $time, $amount)
    {
        $data_route = Route::where('id', $route_id)
            ->with(['subcategory.excursions'=>function($query) use($date, $time){
                /** @var Excursion $query */
                $query->where('date', $date)
                    ->where('time', $time);
            }])
            ->first();

        $busy_seat_place = 0;
        if(!empty($data_route->subcategory->excursions)){
            foreach($data_route->subcategory->excursions as $key => $value){
                $busy_seat_place += (int)$value->people;
            }
        }

        return $amount-$busy_seat_place;
    }

    static public function getTimesForRoutes()
    {
        $times = Order::where('category_id', Category::DJIPPING)->distinct('time')->orderBy('time')->pluck('time');
        $excursionCarTimetable = ExcursionCarTimetable::whereHas('route', function ($query) {
            return $query->where('category_id', Category::DJIPPING);
        })->distinct('time')->orderBy('time')->pluck('time');

        $times = $times->merge($excursionCarTimetable)->unique()->sort()->values();

        $times = $times->map(function ($item) {
            preg_match('/\d{2}:\d{2}/', $item, $test);
            return $test[0];
        });

        return $times;
    }
}
