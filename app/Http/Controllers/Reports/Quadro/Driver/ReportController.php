<?php

namespace App\Http\Controllers\Reports\Quadro\Driver;

use App\Category;
use App\Exports\Quadro\Driver\GeneralExport;
use App\Order;
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

        // Если в фильтре были выбраны водители
        if ($request->filled('drivers')) {
            $drivers->whereIn('id', $request->input('drivers'));
        }

        $drivers = $drivers->where(function ($sub) use ($request) {
            $sub->whereHas('driver_orders', function($query) use ($request) {
                $query->where('category_id', Category::QUADBIKE);
            })->orWhereHas('refuse_orders', function($query) use ($request) {
                $query->where('category_id', Category::QUADBIKE);
            });
        })->get();

        foreach ($drivers as $driver) {
            $order = $driver->driver_orders()->where('status_id', 4);
            if ($request->filled('start')) {
                $order = $order->whereDate('date', '>=', $request->input('start'));
            }
            if ($request->filled('end')) {
                $order = $order->whereDate('date', '<=', $request->input('end'));
            }

            $accept_count = $order->where('status_id', 4)->get();

            $driver->accept_count = 0;
            /** @var Order $item */
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

}
