<?php

namespace App\Http\Controllers\Reports\Quadro\Manager;

use App\Category;
use App\Core\Reports\DeletedOrder as DeletedOrderService;
use App\Core\Reports\Order as OrderService;
use App\Exports\DeletedOrder\CategoryExport;
use App\Exports\Quadro\Manager\GeneralExport;
use App\Exports\Quadro\Manager\OrdersExport;
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
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function general(Request $request) :JsonResponse
    {
        // Квадро
        $routes = Route::where('category_id', Category::QUADBIKE)->get();
        $managers = User::query();
        $managers = $managers->manager();

        // Если в фильтре были выбраны менеджеры
        if ($request->filled('managers')) {
            $managers->whereIn('id', $request->input('managers'));
        }

        $managers = $managers->whereHas('orders', function($query) use ($request) {
            $query->where('category_id', Category::QUADBIKE);
            if ($request->filled('start')) {
                $query->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $request->input('start'))->startOfDay());
            }
            if ($request->filled('end')) {
                $query->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $request->input('end'))->endOfDay());
            }
        })->get();

        $array = [];
        $total = [];
        foreach ($managers as $manager) {
            $array['managers'][$manager->id]['name'] = $manager->name;
            foreach ($routes as $route) {
                $order_accept = Order::query();
                $order_accept = $order_accept->where('category_id', Category::QUADBIKE);
                $order_accept = $order_accept->where('manager_id', $manager->id);
                $order_reject = clone $order_accept;

                    if ($request->filled('start')) {
                        $order_accept = $order_accept->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $request->input('start'))->startOfDay());
                    }
                    if ($request->filled('end')) {
                        $order_accept = $order_accept->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $request->input('end'))->endOfDay());
                    }

                $order_reject = clone $order_accept;
                $order_accept = $order_accept
                    ->where('route_id', $route->id)
                    ->whereIn('status_id', [1, 2, 3, 4, 6])->get();

                $order_reject = $order_reject->where('route_id', $route->id)->get();

                $accept = 0;
                $reject = 0;

                foreach ($order_accept as $item) {
                    $accept += $item->peopleSum();
                }
                foreach ($order_reject as $item) {
                    $reject += $item->peopleSum();
                }

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
    public function generalExcel(Request $request) : BinaryFileResponse
    {
        $array = json_decode($request->input('managers'));

        return Excel::download(new GeneralExport($array), 'managers.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDeletedOrders(Request $request): JsonResponse
    {
        $orders = DeletedOrderService::create([
            'category' => Category::QUADBIKE,
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
            'category' => Category::QUADBIKE,
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
