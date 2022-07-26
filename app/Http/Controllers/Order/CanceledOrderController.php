<?php

namespace App\Http\Controllers\Order;

use App\Exports\CanceledOrders\GeneralExport;
use App\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CanceledOrderController extends Controller
{
    /**
     * @param Request $request
     * @return array|Factory|View|mixed
     */
    public function index(Request $request)
    {
        return view('orders.canceled-orders.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrders(Request $request)
    {
        $orders = Order::query();
        if ($request->filled('category_id')) {
            $orders = $orders->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('route')) {
            $orders = $orders->where('route_id', $request->input('route'));
        }

        if ($request->filled('manager')) {
            $orders = $orders->where('manager_id', $request->input('manager'));
        }

        if ($request->filled('driver')) {
            $orders = $orders->where('driver_id', $request->input('driver'));
        }

//        dd($request->input());
        if ($request->filled('start_date') and $request->filled('end_date')) {
            $orders = $orders->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
        }

        if ($request->filled('time')) {
            $time_array = explode(":", $request->input('time'));
            $time = Carbon::createFromTime($time_array[0], $time_array[1])->format('H:i');
            $orders = $orders->where('time', 'like', $time . '%');
        }

        $sort_array = $request->input('sort_orders');

        $orders = $orders->whereIn('status_id', $sort_array)->with(['refuser', 'manager', 'client', 'route', 'excursion', 'driver.cars']);

        $orders = $orders->get();

        return response()->json($orders);
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public function generalExcel(Request $request)
    {
        $array = json_decode($request->input('orders'));

        return Excel::download(new GeneralExport($array), 'canceled-orders.xlsx');
    }
}
