<?php

namespace App\Http\Controllers\Reports;

use App\Category;
use App\Order;
use App\Route;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait GeneralReportTrait
{
    private function generalReport(Request $request, $category): array
    {
        set_time_limit(0);
        $routes = Route::where('category_id', $category)
            ->when($request->input('subcategory'), function (Builder $query, $subcategory) {
                $query->where('subcategory_id', '=', $subcategory);
            })
            ->get();

        $manager = $request->input('managers');
        $managers = User::query()
            ->when($manager, function ($query, $manager) {
                return $query->whereIn('id', $manager);
            });

        $managers = $managers->manager();
        $managersDoesntHaveOrders = clone $managers->manager();

        $managers = $managers->whereHas('orders', function ($query) use ($request, $category) {
            /** @var Order $query */
            $query->where('category_id', $category)->byDates($request);
        })->get();

        $managersDoesntHaveOrders = $managersDoesntHaveOrders->whereDoesntHave('orders', function ($query) use ($request, $category) {
            /** @var Order $query */
            $query->where('category_id', $category)->byDates($request);
        })->get();

        $managers = $managers->merge($managersDoesntHaveOrders);

        $managers = $managers->sortBy('name')->values();


        $array = [];
        $total = [];
        foreach ($managers as $key => $manager) {
            $array['managers'][$key]['name'] = $manager->name;
            foreach ($routes as $route) {
                $days = [];

                $order_accept = Order::query()
                    ->where('category_id', $category)
                    ->where('manager_id', $manager->id)
                    ->where('route_id', $route->id)
                    ->whereNotIn('status_id', [7])
//                    ->whereIn('status_id', [1, 2, 3, 4, 6])
                    ->byDates($request)
                    ->orderBy('created_at')
                    ->get();

                if (
                    $request->filled('days') &&
                    json_decode($request->input('days')) &&
                    $category == Category::SEA
                ) {
                    foreach ($order_accept as $item) {
                        $day = $item->created_at->format('Y-m-d');
                        if (isset($days[$day])) {
                            $days[$day]['accept'] = $days[$day]['accept'] + $item->people_sum;
                        } else {
                            $days[$day]['accept'] = $item->people_sum;
                            $days[$day]['reject'] = 0;
                        }
                    }
                }

                $order_reject = Order::query()
                    ->where('category_id', $category)
                    ->where('manager_id', $manager->id)
                    ->where('route_id', $route->id)
//                    ->where('refuser_id', $manager->id)
                    ->whereIn('status_id', [5])
//                    ->whereIn('status_id', [5, 7])
                    ->whereNotIn('refuser_id', User::Driver()->pluck('id'))
                    ->byDates($request)
                    ->orderBy('created_at')
                    ->get();

                if (
                    $request->filled('days') &&
                    json_decode($request->input('days')) &&
                    $category == Category::SEA
                ) {
                    foreach ($order_reject as $item) {
                        $day = $item->created_at->format('Y-m-d');
                        if (isset($days[$day])) {
                            $days[$day]['reject'] = $days[$day]['reject'] + $item->people_sum;
                        } else {
                            $days[$day]['reject'] = $item->people_sum;
                            if (!array_key_exists('accept', $days[$day])) {
                                $days[$day]['accept'] = 0;
                            }
                        }
                    }
                }

                $accept = $order_accept->sum('people_sum');
                $reject = $order_reject->sum('people_sum');

                $array['managers'][$key]['routes'][$route->id]['id'] = $route->id;
                $array['managers'][$key]['routes'][$route->id]['name'] = $route->name;
                $array['managers'][$key]['routes'][$route->id]['color'] = $route->color;
                $array['managers'][$key]['routes'][$route->id]['accept'] = $accept;
                $array['managers'][$key]['routes'][$route->id]['rejectorder'] = $reject;
                $array['managers'][$key]['routes'][$route->id]['days'] = $days;

                if (isset($total[$route->id])) {
                    $total[$route->id]['accept'] = $total[$route->id]['accept'] + $accept;
                    $total[$route->id]['reject'] = $total[$route->id]['reject'] + $reject;
                } else {
                    $total[$route->id]['accept'] = $accept;
                    $total[$route->id]['reject'] = $reject;
                }
            }
        }

        if (
            $request->filled('days') &&
            json_decode($request->input('days')) &&
            $category == Category::SEA
        ) {
            foreach ($array['managers'] as &$manager) {
                if (array_key_exists('routes', $manager)) {
                    foreach ($manager['routes'] as &$route) {
                        foreach ($this->getPeriod($request, $category) as $date) {
                            if (!array_key_exists($date->format('Y-m-d'), $route['days'])) {
                                $route['days'][$date->format('Y-m-d')] = [
                                    'accept' => 0,
                                    'reject' => 0,
                                ];
                            }
                        }
                        ksort($route['days']);
                    }
                }
            }
        }

        $array['total'] = $total;

        return $array;
    }

    /**
     * @param Request $request
     * @param int $category
     * @return CarbonPeriod
     */
    private function getPeriod(Request $request, int $category): CarbonPeriod
    {
        $start = $request->input('start');
        $end = $request->input('end');

        if (!$start) {
            $firstOrder = Order::query()
                ->where('category_id', $category)
                ->orderBy('created_at')
                ->first();
            if ($firstOrder) {
                $start = $firstOrder->created_at;
            } else {
                $start = Carbon::now();
            }
        }

        if (!$end) {
            $end = Carbon::now();
        }

        return CarbonPeriod::create($start, $end);
    }
}
