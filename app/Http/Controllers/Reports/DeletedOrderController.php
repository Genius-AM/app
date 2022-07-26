<?php

namespace App\Http\Controllers\Reports;

use App\Category;
use App\Core\Reports\DeletedOrder as DeletedOrderService;
use App\Exports\AgeCategory\ReportExport;
use App\Exports\DeletedOrder\GeneralExport;
use App\Models\Company;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DeletedOrderController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $categories = Category::with('subcategories')->get();

        $companies = Company::get();

        return view('reports.deleted-order', compact('categories', 'companies'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $data = [];

        foreach ($request->input('categories') as $category) {
            $categoryData = DeletedOrderService::create([
                'category' => $category,
                'subcategories' => $request->input('subcategories')[$category] ?? [],
                'companies' => $request->input('companies')[$category] ?? [],
                'start' => $request->input('start'),
                'end' => $request->input('end'),
            ])->get();

            $data = array_merge($data, $categoryData);
        }

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Maatwebsite\Excel\BinaryFileResponse|BinaryFileResponse
     * @throws Exception
     * @throws WriterException
     */
    public function excel(Request $request)
    {
        return Excel::download(new GeneralExport(
            $request->input('orders')
        ), 'deleted-orders.xlsx');
    }
}
