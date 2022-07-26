<?php

namespace App\Http\Controllers\API\Drivers\Sea;

use App\Category;
use App\Core\Booked\BookedCarOptions;
use App\Core\Categories\ChangeAmountFactory;
use App\Http\Requests\ChangeAmountRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class SeaController
{
    /**
     * * @SWG\Post(
     *     path="/Driver/Sea/PassengersAmount/Set",
     *     tags={"drivers, sea"},
     *     summary="Установка максимального кол-ва мест на определенный рейс",
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
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     */
    /**
     * Добавление или изменений количесвта брони за определенный день и рейс
     *
     * @param ChangeAmountRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function changeOrAddPassengersAmountInExcursion(ChangeAmountRequest $request)
    {
        $changeMethod = ChangeAmountFactory::getChangeMethod(Category::SEA, $request->input());
        $result = $changeMethod->changeAmount();

        return response()->json(['message' => $result ? 'Данные сохранены' : 'Превышено максимальное количество мест'], $result ? 200 : 400);
    }

    /**
     * @SWG\Post(
     *     path="/Driver/Sea/BookTime/Set",
     *     tags={"drivers, sea"},
     *     summary="Добавляем или изменяем на противоположное значение брони",
     *     description="Добавляем или изменяем на противоположное значение брони",
     *     @SWG\Parameter(
     *         name="time_id",
     *         in="path",
     *         description="Time id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="date",
     *         in="path",
     *         description="date",
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
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changeOrAddBookData(Request $request): JsonResponse
    {
        $result = new BookedCarOptions();

        $result = $result->changeOrAddBookData($request->input('date'), $request->input('time_id'));

        if ($result) {
            return response()->json();
        }

        return response()->json(['message' => 'Данных не достаточно']);
    }

}