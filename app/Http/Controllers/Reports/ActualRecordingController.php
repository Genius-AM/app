<?php

namespace App\Http\Controllers\Reports;

use App\Category;
use App\Core\Reports\ActualRecording;
use App\Exports\ActualRecording\ReportExport;
use App\Models\Company;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ActualRecordingController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $categories = Category::with('subcategories')->get();

        $managers = User::manager()->get();

        $companies = Company::whereIn('name', ['Оксана', 'Коля'])->get();

        return view('reports.actual-recording', compact('categories', 'managers', 'companies'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $data = [];

        foreach ($request->input('categories') as $category) {
            $category = Category::find($category);

            $data[] = [
                'id' => $category->id,
                'name' => $category->name,
                'data' => ActualRecording::create([
                    'category' => $category->id,
                    'subcategories' => $request->input('subcategories')[$category->id] ?? [],
                    'managers' => $request->input('managers')[$category->id] ?? [],
                    'companies' => $request->input('companies')[$category->id] ?? [],
                    'start' => $request->input('start'),
                    'end' => $request->input('end'),
                    'days' => true,
                    'hours' => true,
                ])->get(),
            ];
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
            $this->getManagers($request->input('report')),
            $request->input('categories'),
            $request->input('days'),
            $request->input('hours')
        ), 'actual-recording.xlsx');
    }

    /**
     * @param array $report
     * @return array
     */
    private function getManagers(array $report): array
    {
        $managers = [];

        foreach($report as $item) {
            foreach ($item['data']['managers'] as $manager) {
                $managers[] = [
                    'id' => $manager['id'],
                    'name' => $manager['name'],
                ];
            }
        }

        $managers = Collection::make($managers);

        $managers = $managers->unique(function ($item) {
            return $item['name'].$item['id'];
        });

        return $managers->sortBy('name')->values()->toArray();
    }
}
