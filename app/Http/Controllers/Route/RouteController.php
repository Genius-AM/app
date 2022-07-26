<?php

namespace App\Http\Controllers\Route;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRouteRequest;
use App\Http\Requests\Route\RouteTimesSetRequest;
use App\Route;
use App\Subcategory;
use App\Time;
use App\Day;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouteController extends Controller
{
    /**
     * @return array|Factory|View|mixed
     */
    public function new()
    {
        $categories = Category::all();

        $colors = json_encode(Route::colors);

        return view('routes.newRoute', compact('categories', 'colors'));
    }

    /**
     * @return array|Factory|View|mixed
     */
    public function get()
    {
        $routes = Route::all();
        $routes->load('subcategory.category');

        return view('routes.routelist', ['routes' => $routes]);
    }

    /**
     * @param null $id
     * @return array|Factory|View|mixed
     */
    public function show($id = null)
    {
        $route = Route::findOrFail($id);

        $colors = Route::colors;

        return view('routes.editRoute', compact('route', 'colors'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $route = Route::findOrFail($request->input('route'));
        $route->name = $request->input('name');
        $route->price_men = $request->input('price_men');
        $route->price_women = $request->input('price_women');
        $route->price_kids = $request->input('price_kids');
        $route->price = $request->input('price');
        $route->color = $request->input('color');
        $route->is_payable = false;
        $route->duration = Carbon::createFromTimeString($request->input('duration'))->format('H:i:s');
        if ($request->input('is_payable')) {
            $route->is_payable = true;
        }
        $route->save();

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request)
    {
        Route::destroy($request->input('route'));

        return redirect()->back();
    }

    /**
     * @param CreateRouteRequest $request
     * @return RedirectResponse
     */
    public function create(CreateRouteRequest $request)
    {
        $route = new Route();
        $route->name = $request->input('name');
        $route->category_id = $request->input('category_id');
        $route->subcategory_id = $request->input('subcategory_id');
        $route->price_men = $request->input('price_men');
        $route->price_women = $request->input('price_women');
        $route->price_kids = $request->input('price_kids');
        $route->price = $request->input('price');
        $route->prepayment = $request->input('prepayment');
        $route->is_payable = false;
        $route->duration = Carbon::createFromTimeString($request->input('duration'))->format('H:i:s');
        $route->color = $request->input('color');
        if ($request->input('is_payable')) {
            $route->is_payable = true;
        };
        $route->save();

        return response()->json(['message' => 'Маршрут добавлен'], 200);
    }

    /**
     * @return array|Factory|View|mixed
     */
    public function index()
    {
        $categories = Category::all();
        $times = Time::all();

        return view('newTimes', ['categories' => $categories, 'times' => $times]);
    }

    /**
     * @return array|Factory|View|mixed
     */
    public function index2()
    {
        $categories = Category::all();
        $times = Time::all();

        return view('newTimesDispatcher', ['categories' => $categories, 'times' => $times]);
    }

    /**
     * @param RouteTimesSetRequest $request
     * @return RedirectResponse
     */
    public function timesSet(RouteTimesSetRequest $request)
    {
        $days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
        $route = Route::find($request->input('route'));
        foreach ($route->days()->wherePivot('car_id', $request->input('car'))->get() as $day) {
            $day->times()->detach();
            Day::destroy($day->id);
        }
        $route->days()->detach();

        foreach ($request->input('dates') as $index => $date) {
            $day = new Day(['name' => $days[$index], 'weekday' => $date]);
            $day->save();
            $route->days()->attach($day->id, ['car_id' => $request->input('car')]);
            if (isset($request->times[$date])) {
                $sync = [];
                foreach ($request->times[$date] as $key => $time) {
                    $sync[$request->times[$date][$key]] = ['amount' => $request->amount[$date][$key]];
                }
                $day->times()->sync($sync);
                $day->save();
            }

            $route->save();
        }

        $route->route_timetables()->delete();
        if ($request->filled('datetime')) {
            foreach ($request->input('datetime') as $index => $value) {
                $dateTime = Carbon::parse($value);

                $route->route_timetables()->create([
                    'date' => $dateTime->format('Y-m-d'),
                    'time' => $dateTime->format('H:i'),
                    'amount' => $request->input('datetime_amount')[$index],
                ]);
            }
        }

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getRouteTimes(Request $request)
    {
        $carId = $request->input('car_id');

        $times = Route::where('id', $request->input('id'))
            ->where(function (Builder $query) use ($carId) {
                $query->where('category_id', '!=', 4)
                    ->orWhere(function (Builder $query) use ($carId) {
                        $query->where('category_id', '=', 4)
                            ->whereHas('days', function (Builder $query) use ($carId) {
                                $query->where('day_route.car_id', '=', $carId);
                            });
                    });
            })
            ->with('days.times', 'route_timetables')
            ->get();

        return response()->json($times);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getRoutes(Request $request)
    {
        $routes = Route::where('subcategory_id', $request->input('id'))->get();

        return response()->json($routes);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getSubcategories(Request $request)
    {
        $subcategories = Subcategory::where('category_id', $request->input('id'))->get();

        return response()->json($subcategories);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCars(Request $request)
    {
        $route = Route::find($request->input('id'));

        return response()->json($route->cars ?? []);
    }
}
