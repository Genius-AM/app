<?php

namespace App\Http\Controllers\Reports;

use App\Category;
use App\Core\Reports\AgeCategory as AgeCategoryService;
use App\Core\Reports\HourRecording as HourRecordingService;
use App\Exports\AgeCategory\ReportExport;
use App\Models\AgeCategory;
use App\Models\Company;
use App\User;
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

class AgeCategoryController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $categories = Category::with('subcategories')->get();

        $managers = User::manager()->get();

        $companies = Company::whereIn('name', ['Оксана', 'Коля'])->get();

        $ageCategories = AgeCategory::query()
            ->orderBy('from')
            ->orderBy('to')
            ->get();

        return view('reports.age-category', compact('categories', 'managers', 'companies', 'ageCategories'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $data = [];

        $ageCategories = AgeCategory::query()
            ->orderBy('from')
            ->orderBy('to')
            ->get();
        foreach ($ageCategories as $ageCategory) {
            $data['ages'][$ageCategory->id] = 0;
        }
        $data['ages']['amount'] = 0;
        $data['categories'] = [];

        foreach ($request->input('categories') as $category) {
            $category = Category::find($category);

            $categoryData = AgeCategoryService::create([
                'category' => $category->id,
                'subcategories' => $request->input('subcategories')[$category->id] ?? [],
                'managers' => $request->input('managers') ?? [],
                'companies' => $request->input('companies')[$category->id] ?? [],
                'start' => $request->input('start'),
                'end' => $request->input('end'),
                'days' => true,
                'hours' => true,
            ])->get();

            $data['categories'][] = [
                'id' => $category->id,
                'name' => $category->name,
                'data' => $categoryData,
            ];

            foreach ($ageCategories as $ageCategory) {
                $data['ages'][$ageCategory->id] += $categoryData['ages'][$ageCategory->id];
                $data['ages']['amount'] += $categoryData['ages'][$ageCategory->id];
            }

            $data['global'][$category->id] = HourRecordingService::create([
                'category' => $category->id,
                'subcategories' => $request->input('subcategories')[$category->id] ?? [],
                'managers' => $request->input('managers') ?? [],
                'companies' => $request->input('companies')[$category->id] ?? [],
                'start' => $request->input('start'),
                'end' => $request->input('end'),
            ])->get();
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
        return Excel::download(new ReportExport(
            $request->input('report'),
            $request->input('days'),
            $request->input('hours')
        ), 'actual-recording.xlsx');
    }
}
