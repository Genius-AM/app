<?php

namespace App\Http\Controllers\Cars\Timetable;

use App\Core\Timetable\TimetableOptions;
use App\Excursion;
use App\Models\ExcursionCarTimetable;
use App\Models\RouteCar;
use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimetableController extends Controller
{
    public function index(Request $request, RouteCar $routeCar)
    {
        $car = $routeCar->car;
        $route = $routeCar->route;

        return view('cars.timetable.index', compact('routeCar', 'car', 'route'));
    }

    /**
     * @param Request $request
     * @param RouteCar $routeCar
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualTimetable(Request $request, RouteCar $routeCar)
    {
        /** @var ExcursionCarTimetable $exc_car_timetable */
        $exc_car_timetable = ExcursionCarTimetable::query();

        if ($request->filled('date')) {
            $date = $request->input('date');
            $exc_car_timetable = $exc_car_timetable->where(function ($quer) use ($date, $routeCar) {
                $quer->where('car_id', $routeCar->car_id)
                    ->where('route_id', $routeCar->route_id)->where('date', $date);

                // если ничего нет,
                // добавляет стандартное время по дню недели
                if (!$quer->count()) {
                    $weekday = strtolower(Carbon::createFromFormat('Y-m-d', $date)->locale('en_En')->isoFormat('dddd'));
                    $quer->orWhere(function($query) use ($weekday, $routeCar) {
                        $query->where('car_id', $routeCar->car_id)
                            ->where('route_id', $routeCar->route_id)->where('date', null)->where('day', $weekday);
                    });
                }
            });
        } else {
            $exc_car_timetable = $exc_car_timetable->where('car_id', $routeCar->car_id)
                ->where('route_id', $routeCar->route_id)->where('date', null);
        }

        $exc_car_timetable = $exc_car_timetable->get()->groupBy('day');

        $collect = TimetableOptions::createEmptyTimetable()->merge($exc_car_timetable);

        return response()->json($collect, 200);
    }

    /**
     * @param Request $request
     * @param ExcursionCarTimetable $excursionCarTimetable
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request, ExcursionCarTimetable $excursionCarTimetable)
    {
        if ($request->filled('date')) {
            $date = Carbon::createFromFormat('d.m.Y', $request->input('date'))->format('Y-m-d');
        }
        $date = Carbon::now()->format('Y-m-d');

        if ($excursionCarTimetable->excursions()->active()->where('date', '>', $date)->count()) {
            return response()->json(['errors' => 'Невозможно удалить, есть экскурсии позже текущей даты'], 400);
        }

        return response()->json(['OK'], 200);
    }

    /**
     * @param Request $request
     * @param RouteCar $routeCar
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function save(Request $request, RouteCar $routeCar)
    {
        // сначала удалить ненужные
        foreach ($request->input('deleted') as $deleted) {
            if ($deleted['id']) {
                $timetable = ExcursionCarTimetable::findOrFail($deleted['id']);

                if (Excursion::where('exc_car_timetable_id', $deleted['id'])->exists()) {
                    return response()->json([
                        'errors' => sprintf('Невозможно удалить время: %s, есть связанные экскурсии.', $deleted['time'])
                    ], 400);
                }

                $timetable->delete();
            };
        }

        foreach ($request->input('phase') as $changed) {
            // изменить существующие
            if ($changed['id'] and $changed['changed'] == true) {
                $timetable = ExcursionCarTimetable::findOrFail($changed['id']);

                // @TODO: Убрать дублирование кода
                if ($timetable->date === null && $request->filled('date')) {
                    $timetable = new ExcursionCarTimetable();
                    $timetable->car_id   = $routeCar->car_id;
                    $timetable->route_id = $routeCar->route_id;
                    $timetable->day      = $request->input('weekday');
                    $timetable->date     = $request->input('date');
                }

                $timetable->time = Carbon::createFromTimeString($changed['time'])->format('H:i:s');
                $timetable->self = true;
                $timetable->save();

                // У экскурсий и заявок
                /** @var Excursion $excursion */
                foreach ($timetable->excursions()->active()->get() as $excursion) {
                    $excursion->time = Carbon::createFromTimeString($changed['time'])->format('H:i:s');
                    $excursion->save();
                    /** @var Order $order */
                    foreach ($excursion->orders as $order) {
                        $order->time = Carbon::createFromTimeString($changed['time'])->format('H:i:s');
                        $order->save();
                    }
                }
            };

            // добавить новые
            // если остается от стандартного
            if ($changed['id'] and $changed['changed'] == false) {
                $timetable = ExcursionCarTimetable::findOrFail($changed['id']);
                if ($request->filled('date') and !$timetable->date) {
                    $timetable = new ExcursionCarTimetable();
                    $timetable->car_id = $routeCar->car_id;
                    $timetable->route_id = $routeCar->route_id;
                    $timetable->day = $request->input('weekday');
                    $timetable->time = Carbon::createFromTimeString($changed['time'])->format('H:i:s');
                    $timetable->date =$request->input('date');
                    $timetable->self = true;
                    $timetable->save();
                }
            }

            // новые
            if ($changed['id'] == null and $changed['changed'] == true) {
                $timetable = new ExcursionCarTimetable();
                $timetable->car_id = $routeCar->car_id;
                $timetable->route_id = $routeCar->route_id;
                $timetable->day = $request->input('weekday');
                $timetable->time = Carbon::createFromTimeString($changed['time'])->format('H:i:s');
                $timetable->date =$request->input('date');
                $timetable->self = true;
                $timetable->save();
            }
        }

        return response()->json(['Данные изменены'], 200);
    }
}
