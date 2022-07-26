<?php


namespace App\Core\Timetable;


use App\Cars;
use App\Models\ExcursionCarTimetable;
use App\Route;
use Illuminate\Support\Carbon;

class TimetableOptions
{
    /**
     * @param Route $route
     * @param Cars $car
     */
    public static function add(Route $route, Cars $car)
    {
        foreach ($route->days as $day) {
            foreach ($day->times as $time) {
                $exc_car_timetable = new ExcursionCarTimetable();
                $exc_car_timetable->car_id = $car->id;
                $exc_car_timetable->route_id = $route->id;
                $exc_car_timetable->day = $day->weekday;
                $exc_car_timetable->time = Carbon::createFromFormat('H:i', $time->name)->format('H:i:00');
                $exc_car_timetable->save();
            }
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function createEmptyTimetable()
    {
        $array = collect([
            'monday' => collect(),
            'tuesday' => collect(),
            'wednesday' => collect(),
            'thursday' => collect(),
            'friday' => collect(),
            'saturday' => collect(),
            'sunday' => collect(),
        ]);

        return $array;
    }

    /**
     * @param $car_id
     * @param $route_id
     * @param $time
     * @param $date
     * @return array
     */
    public static function excCarTimetable($car_id, $route_id, $time, $date)
    {
        $weekday = strtolower(Carbon::parse($date)->locale('en_En')->isoFormat('dddd'));
        $time = Carbon::createFromTimeString($time)->format('H:i:00');

        $excExcursionCarTimetable = ExcursionCarTimetable::where('car_id', $car_id)
            ->where('route_id', $route_id)
            ->where('day', $weekday)
            ->where('time', $time)
            ->where('date', $date)->first();


        if (!$excExcursionCarTimetable) {
            $excExcursionCarTimetable = ExcursionCarTimetable::where('car_id', $car_id)
                ->where('route_id', $route_id)
                ->where('day', $weekday)
                ->where('time', $time)->first();
        }

        return $excExcursionCarTimetable;
    }
}