<?php

namespace App\Http\Controllers\Route;

use App\Cars;
use App\Core\Timetable\TimetableOptions;
use App\Http\Requests\Route\RouteCarCreateRequest;
use App\Models\RouteCar;
use App\Route;
use App\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RouteCarController extends Controller
{
    /**
     * @param Request $request
     * @param Route $route
     * @return array|Factory|View|mixed
     */
    public function index(Request $request, Route $route)
    {
        $route_cars = $route->route_car()->get();

        return view('routes.route-car-index', ['route_cars' => $route_cars, 'route' => $route] );
    }

    /**
     * @param Request $request
     * @param Route $route
     * @param RouteCar $routeCar
     * @return array|Factory|View|mixed
     */
    public function add(Request $request, Route $route, RouteCar $routeCar)
    {
        $cars_in_route = $route->cars()->get()->pluck('id')->diff($routeCar->car_id);
        $driversByCategory = User::where('category_id', $route->category_id)->get()->pluck('id');

        $cars = Cars::with('driver.company')
            ->whereNotIn('id', $cars_in_route)
            ->whereIn('driver_id', $driversByCategory)
            ->get();

        return view('routes.route-car-add', ['route' => $route, 'cars' => $cars, 'routeCar' => $routeCar]);
    }

    /**
     * @param RouteCarCreateRequest $request
     * @param Route $route
     * @param RouteCar $routeCar
     * @return JsonResponse
     */
    public function create(RouteCarCreateRequest $request, Route $route, RouteCar $routeCar)
    {
        $add_in_timetable = false;
        if (!$routeCar->id) {
            $add_in_timetable = true;
        }

        $car = Cars::findOrFail($request->input('car'));
        $routeCar->car_id = $car->id;
        $routeCar->route_id = $route->id;
        $routeCar->price_men = $request->input('price_men');
        $routeCar->price_women = $request->input('price_women');
        $routeCar->price_kids = $request->input('price_kids');
        $routeCar->price = $request->input('price');
        $routeCar->prepayment = $request->input('prepayment');
        $routeCar->is_payable = $request->input('payable');
        $routeCar->duration = $request->input('duration');

        $routeCar->save();

        if ($add_in_timetable) {
            TimetableOptions::add($route, $car);
        }

        return response()->json(['message' => 'Машина к маршруту добавлена'], 200);
    }
}
