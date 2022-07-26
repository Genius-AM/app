<?php


namespace App\Http\Controllers\API\Managers\Quadbike;


use App\BookedTime;
use App\Cars;
use App\Category;
use App\Core\Categories\NewOrderFactory;
use App\Excursion;
use App\Http\Controllers\API\HelperController;
use App\Http\Requests\API\Managers\Quadbike\CreateOrderRequest;
use App\Http\Requests\API\Managers\Quadbike\EditOrderRequest;
use App\Http\Requests\API\Managers\Quadbike\TimesAndLimitsRequest;
use App\Http\Resources\TimeLimits;
use App\Models\Company;
use App\Models\ExcursionCarTimetable;
use App\PassengersAmountInExcursion;
use App\Route;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class QuadbikeController
{
    /**
     * @SWG\Post(
     *     path="/Manager/Quadbike/Order/Add",
     *     tags={"managers, quadbike"},
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
     *         name="company_id",
     *         in="query",
     *         description="company Id",
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
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="women",
     *         in="query",
     *         description="Women amount",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="kids",
     *         in="query",
     *         description="Kids amount",
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
     *         description="ID client address",
     *         required=true,
     *         type="integer",
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
        // Квадрики
        $changeMethod = NewOrderFactory::getChangeMethod(Category::QUADBIKE, $request->input(), $request->user());

        /** @var Response $result */
        $result = $changeMethod->addOrder();

        return response($result->getOriginalContent(), $result->status());
    }

    /**
     * @SWG\Post(
     *     path="/Manager/Quadbike/Order/Edit",
     *     tags={"managers, quadbike"},
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
     *         name="company_id",
     *         in="query",
     *         description="Company Id",
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
        $changeMethod = NewOrderFactory::getChangeMethod(Category::QUADBIKE, $request->input(), $request->user());

        /** @var Response $result */
        $result = $changeMethod->editOrder();

        return response($result->getOriginalContent(), $result->status());
    }

    /**
     * @SWG\Get(
     *     path="/Manager/Quadbike/TimesAndLimits",
     *     summary="Время и лимиты",
     *     description="Время и лимиты",
     *     tags={"managers, quadbike"},
     *   @SWG\Parameter(
     *     name="DateTime",
     *     in="query",
     *     description="DateTime",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="company_id",
     *     in="query",
     *     description="Company ID",
     *     required=false,
     *     type="integer"
     *   ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="route_id", type="integer", description="Route"),
     *                 @SWG\Property(property="company_id", type="integer", description="CompanyID"),
     *                 @SWG\Property(property="DateTime", type="string", description="DateTime"),
     *                 @SWG\Property(property="men", type="number", description="men limit"),
     *                 @SWG\Property(property="women", type="number", description="women limit"),
     *                 @SWG\Property(property="kids", type="number", description="kids limit"),
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
        if ($request->filled('company_id')) {
            $companies = Company::where('id', $request->input('company_id'))->get();
        } else {
            $companies = Company::all();
        }
        $date = Carbon::parse($request->input('DateTime'))->format('Y-m-d');
        $weekday = strtolower(Carbon::parse($date)->locale('en_En')->isoFormat('dddd'));
        $today = Carbon::now();

        $array = [];
        /** @var Company $company */
        foreach ($companies as $company) {
            if ($company->driver and $company->driver->cars->first()) {
                /** @var Cars $car */
                $car = $company->driver->cars->first();
                /** @var Excursion $excursions */
                // новое
                $routes = $car->routes;

                foreach ($routes as $route) {
                    $timetables = $route->timetables()->where('car_id', $car->id)->where('date', $date)->get();

                    if (!$timetables->count()) {
                        $timetables = $route->timetables()->where('car_id', $car->id)->where('day', $weekday)->where('date', null)->get();
                    }

                    /** @var ExcursionCarTimetable $timetable */
                    foreach ($timetables as $timetable) {

                        $time_arr = explode(':', \Illuminate\Support\Carbon::createFromTimeString($timetable->time)->format('H:i'));
                        $time = (int)$time_arr[0] . ':' . $time_arr[1];

                        $check_booked_data = BookedTime::where('route_id', $route->id)
                            ->where('date', $date)
                            ->where('time', $time)
                            ->where('booked', 1)
                            ->first();

                        // если не бронь
                        if (!$check_booked_data) {

                            if ($date === $today->format('Y-m-d')) {
                                if (Carbon::createFromTimeString($timetable->time) < $today) {
                                    continue;
                                }

                                $array = $this->setData($timetable, $route, $date, $company, $car, $array);
                            } else {
                                $array = $this->setData($timetable, $route, $date, $company, $car, $array);
                            }
                        }
                    }
                }
            }
        }

        return TimeLimits::collection(collect($array));
    }

    /**
     * @param ExcursionCarTimetable $timetable
     * @param Route $route
     * @param string $date
     * @param Company $company
     * @param Cars $car
     * @param $array
     * @return array
     */
    private function setData(ExcursionCarTimetable $timetable, Route $route, string $date, Company $company, Cars $car, $array)
    {
        if ($timetable->excursions()->where('date', $date)->count()) {
            foreach ($timetable->excursions()->where('date', $date)->get() as $excursion) {
                //Ищем лимиты по экскурсии
                $data = HelperController::passengersAmountInExcursion(
                    $route->id,
                    $date,
                    $excursion->time,
                    $route->subcategory_id,
                    Category::QUADBIKE,
                    $company->id
                );

                foreach ($excursion->orders()->get() as $order) {
                    $data['men'] = $data['men'] - $order->men;
                    $data['women'] = $data['women'] - $order->women;
                    $data['kids'] = $data['kids'] - $order->kids;
                }

                if ($data['kids'] + $data['men'] + $data['women'] > 0) {
                    $array[] = [
                        'route' => $route->route_car->where('car_id', $car->id)->first(),
                        'company' => $company,
                        'DateTime' => Carbon::createFromFormat('Y-m-dH:i', $date . Carbon::createFromTimeString($timetable->time)->format('H:i'))->toAtomString(),
                        'men' => $data['men'] < 0 ? 0 : $data['men'],
                        'women' => $data['women'] < 0 ? 0 : $data['women'],
                        'kids' => $data['kids'] < 0 ? 0 : $data['kids'],
                    ];
                }
            }
        } else {
            $data = HelperController::passengersAmountInExcursion(
                $route->id,
                $date,
                $timetable->time,
                $route->subcategory_id,
                Category::QUADBIKE,
                $company->id
            );

            $array[] = [
                'route' => $route->route_car->where('car_id', $car->id)->first(),
                'company' => $company,
                'DateTime' => Carbon::createFromFormat('Y-m-dH:i', $date . Carbon::createFromTimeString($timetable->time)->format('H:i'))->toAtomString(),
                'men' => $data['men'],
                'women' => $data['women'],
                'kids' => $data['kids'],
            ];
        }

        return $array;
    }
}