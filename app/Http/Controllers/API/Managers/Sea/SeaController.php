<?php

namespace App\Http\Controllers\API\Managers\Sea;

use App\BookedTime;
use App\Category;
use App\Core\Categories\NewOrderFactory;
use App\Http\Controllers\API\HelperController;
use App\Http\Requests\API\Managers\Sea\CreateOrderRequest;
use App\Http\Requests\API\Managers\Sea\EditOrderRequest;
use App\Http\Requests\API\Managers\Sea\TimesAndLimitsRequest;
use App\Http\Resources\SeaTimeLimits;
use App\Models\ExcursionCarTimetable;
use App\PassengersAmountInExcursion;
use App\Route;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SeaController
{
    /**
     * @SWG\Post(
     *     path="/Manager/Sea/Order/Add",
     *     tags={"managers, sea"},
     *     summary="Добавление новой заявки",
     *     description="Добавление новой заявки",
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
     *         description="Взрослые",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="kids",
     *         in="query",
     *         description="Дети",
     *         required=false,
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
     *         name="client_address",
     *         in="query",
     *         description="Client address",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="client_point_id",
     *         in="query",
     *         description="ID client address",
     *         required=true,
     *         type="integer",
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
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     */
    /**
     * @param CreateOrderRequest $request
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function storeNewOrder(CreateOrderRequest $request)
    {
        $changeMethod = NewOrderFactory::getChangeMethod(Category::SEA, $request->input(), $request->user());

        /** @var Response $result */
        $result = $changeMethod->addOrder();

        return response($result->getOriginalContent(), $result->status());
    }

    /**
     * @SWG\Post(
     *     path="/Manager/Sea/Order/Edit",
     *     tags={"managers, sea"},
     *     summary="Редактирование заявки",
     *     description="Редактирование заявки",
     *     @SWG\Parameter(
     *         name="route_id",
     *         in="query",
     *         description="Route Id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="order_id",
     *         in="query",
     *         description="Editing order id",
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
     *         description="Взрослые",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="kids",
     *         in="query",
     *         description="Дети",
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
     *         name="client_address",
     *         in="query",
     *         description="ID client address",
     *         required=false,
     *         type="integer",
     *         description="Client Address, ID"
     *     ),
     *     @SWG\Parameter(
     *         name="client_point_id",
     *         in="query",
     *         description="ID client address",
     *         required=false,
     *         type="integer",
     *         description="Client Address, ID"
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
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     */
    /**
     * @param EditOrderRequest $request
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function editOrder(EditOrderRequest $request)
    {
        $changeMethod = NewOrderFactory::getChangeMethod(Category::SEA, $request->input(), $request->user());

        /** @var Response $result */
        $result = $changeMethod->editOrder();

        return response($result->getOriginalContent(), $result->status());
    }

    /**
     * @SWG\Get(
     *     path="/Manager/Sea/TimesAndLimits",
     *     summary="Время и лимиты",
     *     description="Время и лимиты",
     *     tags={"managers, sea"},
     *     @SWG\Parameter(
     *          name="DateTime",
     *          in="query",
     *          description="DateTime",
     *          required=true,
     *          type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="subcategory_id",
     *         in="query",
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
     *                 @SWG\Property(property="route_id", type="integer", description="Route"),
     *                 @SWG\Property(property="DateTime", type="string", description="DateTime"),
     *                 @SWG\Property(property="price", type="string", description="Price"),
     *                 @SWG\Property(property="duration", type="string", description="Duration"),
     *                 @SWG\Property(property="men", type="number", description="Взрослые"),
     *                 @SWG\Property(property="kids", type="number", description="Дети"),
     *                 @SWG\Property(property="time_id", type="number", description="Time"),
     *             )
     *         )
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
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     */
    /**
     * @param TimesAndLimitsRequest $request
     * @return AnonymousResourceCollection
     */
    public function timesAndLimits(TimesAndLimitsRequest $request)
    {
        $subcategory_id = $request->input('subcategory_id');
        $date = Carbon::parse($request->input('DateTime'))->format('Y-m-d');
        $weekday = strtolower(Carbon::parse($date)->locale('en_En')->isoFormat('dddd'));
        $today = Carbon::now();

        $routes = Route::where('subcategory_id', $subcategory_id)->get();

        $array = [];

        foreach ($routes as $route) {
            $timetables = $route->timetables()
                ->where('day', $weekday)
                ->where(function (Builder $query) use ($date) {
                    $query->whereNull('date')
                        ->orWhere('date', $date);
                })
                ->get();

            /** @var ExcursionCarTimetable $timetable */
            foreach ($timetables as $timetable) {
                if ($date === $today->format('Y-m-d')) {
                    if (Carbon::createFromTimeString($timetable->time) < $today) {
                        continue;
                    }

                    $array = $this->setData($timetable, $route, $date, $array);
                } else {
                    $array = $this->setData($timetable, $route, $date, $array);
                }
            }
        }

        $array = array_filter($array, function ($item) {
            return $item['booked'] == 0;
        });

        return SeaTimeLimits::collection(collect($array));
    }

    /**
     * @param ExcursionCarTimetable $timetable
     * @param Route $route
     * @param string $date
     * @param $array
     * @return array
     */
    private function setData(ExcursionCarTimetable $timetable, Route $route, string $date, $array): array
    {
        if ($timetable->excursions()->where('date', $date)->count()) {
            foreach ($timetable->excursions()->where('date', $date)->get() as $excursion) {
                //Ищем лимиты по экскурсии
                $data = HelperController::passengersAmountInExcursion(
                    $route->id,
                    $date,
                    $excursion->time,
                    $route->subcategory_id,
                    Category::SEA
                );
                if ($excursion->car_id == 67) {
                    $data['men'] = 66;
                    $data['kids'] = 33;
                }

                foreach ($excursion->orders()->get() as $order) {
                    $data['men'] = $data['men'] - $order->men;
                    $data['kids'] = $data['kids'] - $order->kids;
                }

                if ($data['kids'] + $data['men'] > 0) {
                    $dateTime = Carbon::createFromFormat('Y-m-dH:i', $date . Carbon::createFromTimeString($timetable->time)->format('H:i'))->toAtomString();

                    $key = $this->getKey($array, $route, $dateTime);

                    if ($key === null) {
                        $array[] = [
                            'route' => $route,
                            'DateTime' => $dateTime,
                            'men' => $data['men'] < 0 ? 0 : $data['men'],
                            'women' => 0,
                            'kids' => $data['kids'] < 0 ? 0 : $data['kids'],
                            'time_id' => $timetable->id,
                            'booked' => $timetable->booked,
                        ];
                    } else {
                        if ($timetable->date) {
                            $array[$key]['time_id'] = $timetable->id;
                        }
                        if ($timetable->booked == 1) {
                            $array[$key]['booked'] = 1;
                        }
                    }
                }
            }
        } else {
            $data = HelperController::passengersAmountInExcursion(
                $route->id,
                $date,
                $timetable->time,
                $route->subcategory_id,
                Category::SEA
            );
            if ($timetable->car_id == 67) {
                $data['men'] = 66;
                $data['kids'] = 33;
            }

            $dateTime = Carbon::createFromFormat('Y-m-dH:i', $date . Carbon::createFromTimeString($timetable->time)->format('H:i'))->toAtomString();

            $key = $this->getKey($array, $route, $dateTime);

            if ($key === null) {
                $array[] = [
                    'route' => $route,
                    'DateTime' => $dateTime,
                    'men' => $data['men'],
                    'women' => 0,
                    'kids' => $data['kids'],
                    'time_id' => $timetable->id,
                    'booked' => $timetable->booked,
                ];
            } else {
                if ($timetable->date) {
                    $array[$key]['time_id'] = $timetable->id;
                }
                if ($timetable->booked == 1) {
                    $array[$key]['booked'] = 1;
                }
            }
        }

        return $array;
    }

    /**
     * @param $array
     * @param $route
     * @param $dateTime
     * @return int|null
     */
    private function getKey($array, $route, $dateTime): ?int
    {
        foreach ($array as $key => $item) {
            if (
                $item['route']->id == $route->id &&
                $item['DateTime'] == $dateTime
            ) {
                return $key;
            }
        }

        return null;
    }
}