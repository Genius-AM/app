<?php

namespace App\Exports\Quadro\Manager;

use App\Category;
use App\Route;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GeneralExport implements FromView, ShouldAutoSize
{
    private $managers;

    public function __construct($managers)
    {
        $this->managers = $managers;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $routes = Route::where('category_id', Category::QUADBIKE)->get();

        foreach ($this->managers->managers as $key => $manager) {
            $sumAccept = 0;
            $sumReject = 0;

            foreach ($manager->routes as $route) {
                $sumAccept += $route->accept;
                $sumReject += $route->rejectorder;
            }

            $manager->accept = $sumAccept;
            $manager->reject = $sumReject;
        }

        $totalAccept = 0;
        $totalReject = 0;
        foreach ($this->managers->total as $endtotal) {
            $totalAccept = $totalAccept + $endtotal->accept;
            $totalReject = $totalReject + $endtotal->reject;
        }

        return view('excel.quadro.manager.general', [
            'managers' => $this->managers->managers,
            'total' => $this->managers->total,
            'routes' => $routes,
            'totalaccept' => $totalAccept,
            'totalreject' => $totalReject
        ]);
    }
}
