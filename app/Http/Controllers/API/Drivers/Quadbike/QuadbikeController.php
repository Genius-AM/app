<?php


namespace App\Http\Controllers\API\Drivers\Quadbike;


use App\Category;
use App\Core\Categories\ChangeAmountFactory;
use App\Http\Requests\ChangeAmountRequest;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class QuadbikeController
{
    /**
     * * @SWG\Post(
     *     path="/Driver/Quadbike/PassengersAmount/Set",
     *     tags={"drivers, quadbike"},
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
     * @return ResponseFactory|Response
     * @throws \Exception
     */
    public function changeOrAddPassengersAmountInExcursion(ChangeAmountRequest $request)
    {
        $changeMethod = ChangeAmountFactory::getChangeMethod(Category::QUADBIKE, $request->input(), $request->user()->company);
        $result = $changeMethod->changeAmount();

        return response()->json(['message' => $result ? 'Данные сохранены' : 'Превышено максимальное количество мест'], $result ? 200 : 400);
    }
}