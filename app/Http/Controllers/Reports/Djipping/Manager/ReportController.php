<?php

namespace App\Http\Controllers\Reports\Djipping\Manager;

use App\Category;
use App\Core\Reports\DeletedOrder as DeletedOrderService;
use App\Core\Reports\Order as OrderService;
use App\Core\Route\RouteOptions;
use App\Exports\DeletedOrder\CategoryExport;
use App\Exports\Djipping\Manager\addressExport;
use App\Exports\Djipping\Manager\GeneralExport;
use App\Exports\Djipping\Manager\OrdersExport;
use App\Exports\Djipping\Manager\routeTimeExport;
use App\Http\Controllers\Reports\GeneralReportTrait;
use App\Http\Resources\RouteTime;
use App\Order;
use App\Route;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    use GeneralReportTrait;

    /**
     * Фактическая запись
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function general(Request $request): JsonResponse
    {
        // Джиппинг
        return response()->json($this->generalReport($request, Category::DJIPPING));
    }

    /**
     *
     * @TODO: Временно, Отчёт менеджера
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function general2(Request $request): JsonResponse
    {
        // Джиппинг
        $routes = Route::where('subcategory_id', 1)->get();
        $managers = User::query();

        if ($request->filled('managers')) {
            $managers = $managers->whereIn('id', $request->input('managers'));
        }

        $managers = $managers->manager();

        $managers = $managers->whereHas('orders', function ($query) use ($request) {
            /** @var Order $query */
            $query->where('category_id', Category::DJIPPING);
            if ($request->filled('start')) {
                $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->input('start'))->startOfDay());
            }
            if ($request->filled('end')) {
                $query->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->input('end'))->endOfDay());
            }
        })->get();

        $array = [];
        $total = [];
        foreach ($managers as $manager) {
            $array['managers'][$manager->id]['name'] = $manager->name;
            foreach ($routes as $route) {
                $order_accept = Order::query();

                if ($request->filled('start')) {
                    $order_accept = $order_accept->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->input('start'))->startOfDay());
                }
                if ($request->filled('end')) {
                    $order_accept = $order_accept->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->input('end'))->endOfDay());
                }

                $order_accept = $order_accept
                    ->where('category_id', Category::DJIPPING)
                    ->where('manager_id', $manager->id)
                    ->where('route_id', $route->id)
                    ->whereNotIn('status_id', [7])
                    ->get();

//                $order_accept = $order_accept
//                    ->where('category_id', Category::DJIPPING)
//                    ->where('manager_id', $manager->id)
//                    ->where('route_id', $route->id)
//                    ->whereIn('status_id', [1, 2, 3, 4, 6])
//                    ->get();

                $order_reject = Order::query();
                if ($request->filled('start')) {
                    $order_reject = $order_reject->where('date', '>=', Carbon::createFromFormat('Y-m-d', $request->input('start'))->startOfDay());
                }
                if ($request->filled('end')) {
                    $order_reject = $order_reject->where('date', '<=', Carbon::createFromFormat('Y-m-d', $request->input('end'))->endOfDay());
                }
                
                $order_reject = $order_reject
                    ->where('category_id', Category::DJIPPING)
                    ->where('manager_id', $manager->id)
                    ->where('route_id', $route->id)
//                    ->where('refuser_id', $manager->id)
                    ->whereIn('status_id', [5])
//                    ->whereIn('status_id', [5, 7])
                    ->whereNotIn('refuser_id', User::Driver()->pluck('id'))
                    ->get();

//                $order_reject = $order_reject
//                    ->where('category_id', Category::DJIPPING)
//                    ->where('refuser_id', $manager->id)
//                    ->where('route_id', $route->id)
//                    ->where('status_id', 7)
//                    ->get();

//                $accept = 0;
//                $reject = 0;
//
//                foreach ($order_accept as $item) {
//                    $accept += $item->peopleSum();
//                }
//                foreach ($order_reject as $item) {
//                    $reject += $item->peopleSum();
//                }

                $accept = $order_accept->sum('people_sum');
                $reject = $order_reject->sum('people_sum');

                $array['managers'][$manager->id]['routes'][$route->id]['id'] = $route->id;
                $array['managers'][$manager->id]['routes'][$route->id]['name'] = $route->name;
                $array['managers'][$manager->id]['routes'][$route->id]['color'] = $route->color;
                $array['managers'][$manager->id]['routes'][$route->id]['accept'] = $accept;
                $array['managers'][$manager->id]['routes'][$route->id]['rejectorder'] = $reject;

                if (isset($total[$route->id])) {
                    $total[$route->id]['accept'] = $total[$route->id]['accept'] + $accept;
                    $total[$route->id]['reject'] = $total[$route->id]['reject'] + $reject;
                } else {
                    $total[$route->id]['accept'] = $accept;
                    $total[$route->id]['reject'] = $reject;
                }
            }
        }

        $array['total'] = $total;

        return response()->json($array);
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public function generalExcel(Request $request): BinaryFileResponse
    {
        $array = json_decode($request->input('managers'));

        return Excel::download(new GeneralExport($array), 'managers.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function address(Request $request): JsonResponse
    {
        $array = [];

        /** @var User $managers */
        $managers = User::query()->manager();

        $managers = $managers->with(['orders' => function ($query) use ($request) {
            $query->whereIn('status_id', [1, 2, 3, 4, 6])->where('category_id', 1);
            if ($request->filled('start')) {
                $query->where('created_at', '>=', Carbon::createFromFormat('Y-m-d', $request->input('start'))->startOfDay());
            }
            if ($request->filled('end')) {
                $query->where('created_at', '<=', Carbon::createFromFormat('Y-m-d', $request->input('end'))->endOfDay());
            }
            if ($request->filled('addresses')) {
                $query->whereIn('point_id', $request->input('addresses'));

                foreach ($request->input('addresses') as $address) {
                    if ($address === 'null') {
                        $query->OrWhereNull('point_id');
                    }
                }
            }
        }, 'orders.point'])->get();

        foreach ($managers as $manager) {
            if ($manager->orders->count()) {
                $array[$manager->id]['id'] = $manager->id;
                $array[$manager->id]['name'] = $manager->name;

                /** @var Order $order */
                foreach ($manager->orders as $order) {
                    if (!isset($array[$manager->id]['addresses'][$order->point_id]['total'])) {
                        $array[$manager->id]['addresses'][$order->point_id]['total'] = 0;
                    }
                    $array[$manager->id]['addresses'][$order->point_id]['total'] += $order->peopleSum();
                    $array[$manager->id]['addresses'][$order->point_id]['name'] = $order->point instanceof \App\Address ? $order->point->name : 'Не определен адрес';
                    $array[$manager->id]['addresses'][$order->point_id]['id'] = $order->point_id;
                }
            }
        }

        // Применяем конвертер для преобразования группировки с "менеджеров" на "по адресам"
        $addresses = $this->convertGroupByAddress($array);

        return response()->json($addresses);
    }

    /**
     * Конвертация выборки с "менеджеров" на "по адресам"
     *
     * @param array $managers
     * @return array
     */
    private function convertGroupByAddress(array $managers): array
    {
        $addresses = new class {
            /** @var array */
            private $addresses = [];

            /**
             * Используя входящий адрес возвращаем индекс адреса в $this->addresses,
             * если адреса нету в $this->addresses, то добавляем новый адрес и возвращаем его индекс
             *
             * @param array $inputAddress
             * @return int
             */
            public function getAddressIndexOrCreateAddress(array $inputAddress): int
            {
                foreach ($this->addresses as $addressKey => $address) {
                    if ($address['id'] === $inputAddress['id']) {
                        return $addressKey;
                    }
                }

                $this->addresses[] = [
                    'id' => $inputAddress['id'],
                    'name' => $inputAddress['name'],
                    'managers' => []
                ];

                return array_key_last($this->addresses);
            }

            /**
             * Добавляем нового менеджера по индексу
             *
             * @param int $index
             * @param int $managerId
             * @param string $managerName
             * @param int $addressTotal
             */
            public function push(int $index, int $managerId, string $managerName, int $addressTotal)
            {
                $this->addresses[$index]['managers'][] = [
                    'id' => $managerId,
                    'name' => $managerName,
                    'total' => $addressTotal
                ];

            }

            /** @return array */
            public function get(): array
            {
                return $this->addresses;
            }
        };

        // Проходим по менеджерам
        foreach ($managers as $manager) {
            // Проходим по их адресам
            foreach ($manager['addresses'] as $addressKey => $address) {
                // Получаем индекс адреса, если адреса не найден, то создаём его
                $index = $addresses->getAddressIndexOrCreateAddress($address);

                $addresses->push($index, $manager['id'], $manager['name'], $address['total']);
            }
        }

        return $addresses->get();
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function addressExcel(Request $request): BinaryFileResponse
    {
        $array = json_decode($request->input('addresses'));

        return Excel::download(new addressExport($array), 'addresses.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function routeTime(Request $request): JsonResponse
    {
        $managers = $this->getManagersForRouteTime($request);

        return response()->json($managers);
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function routeTimeExcel(Request $request): BinaryFileResponse
    {
        $managers = $this->getManagersForRouteTime($request);

        return Excel::download(new routeTimeExport($managers->resolve()), 'managers.xlsx');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function getManagersForRouteTime(Request $request)
    {
        $managers = User::manager();

        if ($request->input('managers')) {
            $managers = $managers->whereIn('id', $request->input('managers'));
        }

        $managers = $managers->where('category_id', Category::DJIPPING)->whereHas('orders', function ($query) use ($request) {
            $query->whereIn('status_id', [1, 2, 3, 4, 6])->where('category_id', Category::DJIPPING);
            if ($request->filled('start')) {
                $query->where('created_at', '>=', Carbon::createFromFormat('Y-m-d', $request->input('start'))->startOfDay());
            }
            if ($request->filled('end')) {
                $query->where('created_at', '<=', Carbon::createFromFormat('Y-m-d', $request->input('end'))->endOfDay());
            }
        })->pluck('id');

        $managers = User::manager()->whereIn('id', $managers)->where('category_id', Category::DJIPPING)->with(['orders' => function ($query) use ($request) {
            /** @var Order $query */
            $query->whereIn('status_id', [1, 2, 3, 4, 6])->where('category_id', Category::DJIPPING);
            if ($request->filled('start')) {
                $query->where('created_at', '>=', Carbon::createFromFormat('Y-m-d', $request->input('start'))->startOfDay());
            }
            if ($request->filled('end')) {
                $query->where('created_at', '<=', Carbon::createFromFormat('Y-m-d', $request->input('end'))->endOfDay());
            }
            $query->orderBy('date', 'desc')->orderBy('time', 'desc');
        }])->orderBy('name')->get();


        return RouteTime::collection($managers);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTimesForRouteTime(Request $request)
    {
        $times = RouteOptions::getTimesForRoutes();

        return response()->json($times, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDeletedOrders(Request $request): JsonResponse
    {
        $orders = DeletedOrderService::create([
            'category' => Category::DJIPPING,
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ])->get();

        return response()->json($orders);
    }

    /**
     * @param Request $request
     * @return \Maatwebsite\Excel\BinaryFileResponse|BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function deletedOrdersExcel(Request $request)
    {
        return Excel::download(new CategoryExport($request->input('orders')), 'deleted-orders.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrders(Request $request): JsonResponse
    {
        $orders = OrderService::create([
            'category' => Category::DJIPPING,
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ])->get();

        return response()->json($orders);
    }

    /**
     * @param Request $request
     * @return \Maatwebsite\Excel\BinaryFileResponse|BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function ordersExcel(Request $request)
    {
        return Excel::download(new OrdersExport($request->input('orders')), 'orders.xlsx');
    }
}
