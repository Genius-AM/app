<?php

namespace App\Http\Controllers\Reports\Djipping\Driver;

use App\Category;
use App\Exports\Djipping\Driver\GeneralExport;
use App\Exports\Djipping\Driver\RouteExport;
use App\Order;
use App\Route;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function general(Request $request) : JsonResponse
    {
        $drivers = User::Driver();

        if ($request->filled('drivers')) {
            $drivers = $drivers->whereIn('id', $request->input('drivers'));
        }

        $drivers = $drivers->where(function ($sub) use ($request) {
            $sub->whereHas('driver_orders', function($query) use ($request) {
                $query->where('category_id', Category::DJIPPING);
            })->orWhereHas('refuse_orders', function($query) use ($request) {
                $query->where('category_id', Category::DJIPPING);
            });
        })->get();

        foreach ($drivers as $driver) {
            $order = Order::query();
            $order = $order->where('category_id', Category::DJIPPING);
            $order = $order->where(function ($query) use ($driver) {
                $query->where('refuser_id', $driver->id)->orWhere('driver_id', $driver->id);
            });

            if ($request->filled('start')) {
                $order = $order->where('date', '>=', $request->input('start'));
            }
            if ($request->filled('end')) {
                $order = $order->where('date', '<=', $request->input('end'));
            }

            $reject_count = clone $order;
            $reject_after_accept_count = clone $order;
            $accept_count = clone $order;

            $reject_count = $reject_count->where('status_id', 5)->get();
            $reject_after_accept_count = $reject_after_accept_count->where('status_id', 8)->get();
            $accept_count = $accept_count->where('status_id', 4)->get();

            $driver->reject_count = 0;
            $driver->reject_after_accept_count = 0;
            $driver->accept_count = 0;
            foreach ($reject_count as $item) {
                $driver->reject_count += $item->peopleSum();
            }
            foreach ($reject_after_accept_count as $item) {
                $driver->reject_after_accept_count += $item->peopleSum();
            }
            foreach ($accept_count as $item) {
                $driver->accept_count += $item->peopleSum();
            }
        }

        return response()->json($drivers);
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public function generalExcel(Request $request)
    {
        $array = json_decode($request->input('drivers'));

        return Excel::download(new GeneralExport($array), 'drivers.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function route(Request $request) : JsonResponse
    {
        // Джиппинг
        $routes = Route::where('subcategory_id', 1)->get();
        $drivers = User::query();

        if ($request->filled('drivers')) {
            $drivers = $drivers->whereIn('id', $request->input('drivers'));
        }

        $drivers = $drivers->driver()->with(['driver_orders', 'refuse_orders']);

        $drivers = $drivers->where(function ($sub) use ($request) {
            $sub->whereHas('driver_orders', function($query) use ($request) {
                $query->where('category_id', Category::DJIPPING);
                if ($request->filled('start')) {
                    $query->whereDate('date', '>=', $request->input('start'));
                }
                if ($request->filled('end')) {
                    $query->WhereDate('date', '<=', $request->input('end'));
                }
            })->orWhereHas('refuse_orders', function($query) use ($request) {
                $query->where('category_id', Category::DJIPPING);
                if ($request->filled('start')) {
                    $query->whereDate('date', '>=', $request->input('start'));
                }
                if ($request->filled('end')) {
                    $query->WhereDate('date', '<=', $request->input('end'));
                }
            });
        })->get();

        $array = [];
        $total = [];

        foreach ($drivers as $driver) {
            $array['drivers'][$driver->id]['name'] = $driver->name;
            foreach ($routes as $route) {
                $order_accept = Order::query();
                $order_reject = Order::query();
                $order_reject_after_accept = Order::query();

                $order_accept = $order_accept->where('driver_id', $driver->id);
                $order_reject = $order_reject->where('refuser_id', $driver->id);
                $order_reject_after_accept = $order_reject_after_accept->where('refuser_id', $driver->id);

                if ($request->filled('start')) {
                    $order_accept = $order_accept->whereDate('date', '>=', $request->input('start'));
                    $order_reject = $order_reject->whereDate('date', '>=', $request->input('start'));
                    $order_reject_after_accept = $order_reject_after_accept->whereDate('date', '>=', $request->input('start'));
                }
                if ($request->filled('end')) {
                    $order_accept = $order_accept->whereDate('date', '<=', $request->input('end'));
                    $order_reject = $order_reject->whereDate('date', '<=', $request->input('end'));
                    $order_reject_after_accept = $order_reject_after_accept->whereDate('date', '<=', $request->input('end'));
                }
                $order_accept = $order_accept->where('route_id', $route->id)
                    ->where('status_id', 4)->get();

                $order_reject = $order_reject->where('route_id', $route->id)->where('status_id', 5)->get();

                $order_reject_after_accept = $order_reject_after_accept->where('route_id', $route->id)->where('status_id', 8)->get();

                $accept = 0;
                $reject = 0;
                $reject_after_accept = 0;


                foreach ($order_accept as $item) {
                    $accept += $item->peopleSum();
                }

                foreach ($order_reject as $item) {
                    $reject += $item->peopleSum();
                }

                foreach ($order_reject_after_accept as $item) {
                    $reject_after_accept += $item->peopleSum();
                }

                $array['drivers'][$driver->id]['routes'][$route->id]['id'] = $route->id;
                $array['drivers'][$driver->id]['routes'][$route->id]['name'] = $route->name;
                $array['drivers'][$driver->id]['routes'][$route->id]['color'] = $route->color;
                $array['drivers'][$driver->id]['routes'][$route->id]['accept'] = $accept;
                $array['drivers'][$driver->id]['routes'][$route->id]['rejectorder'] = $reject;
                $array['drivers'][$driver->id]['routes'][$route->id]['rejectafteracceptorder'] = $reject_after_accept;

                if (isset($total[$route->id])) {
                    $total[$route->id]['accept'] = $total[$route->id]['accept'] + $accept;
                    $total[$route->id]['reject'] = $total[$route->id]['reject'] + $reject;
                    $total[$route->id]['rejectafteraccept'] = $total[$route->id]['rejectafteraccept'] + $reject_after_accept;
                } else {
                    $total[$route->id]['accept'] = $accept;
                    $total[$route->id]['reject'] = $reject;
                    $total[$route->id]['rejectafteraccept'] = $reject_after_accept;
                }
            }
        }

        $array['total'] = $total;

        return response()->json($array);
    }


    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function routeExcel(Request $request)
    {
        $array = json_decode($request->input('drivers'));

        return Excel::download(new RouteExport($array), 'drivers.xlsx');
    }
}
