<?php

namespace App\Core\Booked;

use App\Models\ExcursionCarTimetable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class BookedCarOptions
{
    /**
     * @param $date
     * @param $time_id
     * @return bool
     */
    public function changeOrAddBookData($date, $time_id): bool
    {
        $timetable = ExcursionCarTimetable::where('id', $time_id)->first();

        if ($timetable) {
            $carTimetable = ExcursionCarTimetable::where('car_id', $timetable->car_id)
                ->where('route_id', $timetable->route_id)
                ->where('day', $timetable->day)
                ->where('time', $timetable->time)
                ->where('date', $date)
                ->first();

            if (!$carTimetable) {
                $carTimetable = ExcursionCarTimetable::create([
                    'car_id' => $timetable->car_id,
                    'route_id' => $timetable->route_id,
                    'day' => $timetable->day,
                    'time' => $timetable->time,
                    'date' => $date,
                    'self' => $timetable->self,
                    'booked' => $timetable->booked,
                ]);
            }

            $carTimetable->booked = $carTimetable->booked == 0 ? 1 : 0;
            $carTimetable->save();

            return true;
        }

        return false;
    }

    /**
     * @param $date
     * @param $category_id
     * @param $route_id
     * @return array
     */
    public function getTimes($date, $category_id, $route_id): array
    {
        $all_times_with_booked = [];

        if ($category_id && $date) {

            $weekday = strtolower(Carbon::createFromFormat('Y-m-d', $date)->locale('en_En')->isoFormat('dddd'));

            $timetables = ExcursionCarTimetable::where('day', $weekday)
                ->when($route_id, function (Builder $query, $route) {
                    $query->where('route_id', $route);
                })
                ->where(function(Builder $query) use ($date) {
                    $query->whereNull('date')
                        ->orWhere('date', $date);
                })
                ->whereHas('route', function (Builder $query) use ($category_id) {
                    $query->where('category_id', $category_id);
                })
                ->orderByDesc('date')
                ->get();

            foreach ($timetables as $i => $timetable) {
                $key = $this->getKey($all_times_with_booked, $timetable, $weekday);

                if ($key === null) {
                    $all_times_with_booked[] = [
                        'booked' => $timetable->booked,
                        'category_id' => $timetable->route->category_id,
                        'date' => $date,
                        'day' => $weekday,
                        'route_id' => $timetable->route->id,
                        'route_name' => $timetable->route->name,
                        'subcategory_id' => $timetable->route->subcategory_id,
                        'time' => Carbon::createFromFormat('H:i:s', $timetable->time)->format('H:i'),
                        'time_id' => $timetable->id,
                    ];
                } else {
                    if ($timetable->date) {
                        $all_times_with_booked[$key]['time_id'] = $timetable->id;
                    }
                }
            }
        }

        return $all_times_with_booked;
    }

    /**
     * @param $arr
     * @param $timetable
     * @param $weekday
     * @return int|null
     */
    private function getKey($arr, $timetable, $weekday): ?int
    {
        foreach ($arr as $key => $item) {
            if (
                $item['category_id'] == $timetable->route->category_id &&
                $item['subcategory_id'] == $timetable->route->subcategory_id &&
                $item['route_id'] == $timetable->route->id &&
                $item['day'] == $weekday &&
                $item['time'] == Carbon::createFromFormat('H:i:s', $timetable->time)->format('H:i')
            ) {
                return $key;
            }
        }

        return null;
    }
}