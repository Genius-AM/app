<?php

namespace App\Http\Controllers\Reports\Sea\Manager;

use App\Category;
use App\Core\Reports\AdultsChildren;
use App\Core\Reports\DeletedOrder as DeletedOrderService;
use App\Core\Reports\Order as OrderService;
use App\Exports\DeletedOrder\CategoryExport;
use App\Exports\Sea\Manager\AdultsChildrenExport;
use App\Exports\Sea\Manager\GeneralExport;
use App\Exports\Sea\Manager\OrdersExport;
use App\Http\Controllers\Reports\GeneralReportTrait;
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
        return response()->json($this->generalReport($request, Category::SEA));
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
    public function getDeletedOrders(Request $request): JsonResponse
    {
        $orders = DeletedOrderService::create([
            'category' => Category::SEA,
            'subcategories' => [$request->input('subcategory')],
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
            'category' => Category::SEA,
            'subcategories' => [$request->input('subcategory')],
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAdultsChildren(Request $request): JsonResponse
    {
        $orders = AdultsChildren::create([
            'category' => Category::SEA,
            'subcategories' => [$request->input('subcategory')],
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
    public function adultsChildrenExcel(Request $request)
    {
        return Excel::download(new AdultsChildrenExport($request->input('orders')), 'adults-children.xlsx');
    }
}
