<?php

namespace App\Http\Controllers\API\Lists;

use App\Category;
use App\Http\Controllers\Controller;
use \App\Http\Resources\Route as RouteResource;
use App\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RouteController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/Lists/Routes/All",
     *     summary="Получение маршрутов",
     *     description="Получение маршрутов",
     *     tags={"lists"},
     *     @SWG\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Category",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="subcategory_id",
     *         in="query",
     *         description="Subcategory",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Route")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $routes = Route::query();

        if ($request->filled('category_id')) {
            if ($request->filled('subcategory_id')) {
                if (is_array($request->input('subcategory_id'))) {
                    $routes = $routes->whereIn('subcategory_id', $request->input('subcategory_id'));
                } else {
                    $routes = $routes->where('subcategory_id', $request->input('subcategory_id'));
                }
            } else {
                $category = Category::findOrFail($request->input('category_id'));

                $routes = $routes->whereIn('subcategory_id', $category->subcategories()->pluck('id'));
            }
        }

        $routes = $routes->get();

        return RouteResource::collection($routes);
    }
}
