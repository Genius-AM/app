<?php

namespace App\Http\Controllers;

use App\Category;
use App\BookedTime;
use App\Core\Booked\BookedCarOptions;
use App\Core\Booked\BookedOptions;
use App\Models\ExcursionCarTimetable;
use App\Route;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GelenBookController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBookData(Request $request)
    {
        $category_id = $request->input('category_id');
        $date = $request->input('date');

        if (!empty($category_id) && !empty($date)) {

            $bookedOptions = new BookedOptions();
            $all_times = $bookedOptions->getAllTimesDataFromCategory($date, $category_id);

            if (is_array($all_times)) {
                $all_times_with_booked = $bookedOptions->combineAllDataWithBookedTimes($all_times);
                $all_times_with_booked = $bookedOptions->getNormalDataToDispatcher($all_times_with_booked);
                return response()->json($all_times_with_booked);

            } else return response()->json(['error' => 400, 'message' => 'Не все данные в бд']);

        } else return response()->json(['error' => 400, 'message' => 'Данных не достаточно']);
    }

    /**
     * Меняем на противоположное значение брони
     * @param Request $request
     * @return JsonResponse
     */
    public function changeBookData(Request $request)
    {
        $result = new BookedOptions();

        $result = $result->changeOrAddBookData($request->input('category_id'), $request->input('subcategory_id'), $request->input('route_id'), $request->input('date'), $request->input('time'));

        return response()->json(['error' => 200, 'message' => $result]);
    }

    /**
     * Изменяем количество мест
     * @param Request $request
     * @return JsonResponse
     */
    public function amountBookData(Request $request)
    {
        $date = $request->input('date');
        $time = $request->input('time');

        if (strlen($time) < 7) {
            $time .= ':00';
        }

        $route = Route::where('id', $request->input('route_id'))
            ->with([
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
            $route->route_timetables[0]->update(['amount' => $request->input('amount')]);
        } else {
            $route->route_timetables()->create([
                'date' => $date,
                'time' => $time,
                'amount' => $request->input('amount'),
            ]);
        }

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBookDataCar(Request $request): JsonResponse
    {
        $result = new BookedCarOptions();

        $result = $result->getTimes($request->input('date'), $request->input('category_id'), $request->input('route_id'));

        return response()->json($result);
    }

    /**
     * Меняем на противоположное значение брони
     * @param Request $request
     * @return JsonResponse
     */
    public function changeBookDataCar(Request $request): JsonResponse
    {
        $result = new BookedCarOptions();

        $result = $result->changeOrAddBookData($request->input('date'), $request->input('time_id'));

        if ($result) {
            return response()->json();
        }

        return response()->json(['error' => 400, 'message' => 'Данных не достаточно']);
    }
}
