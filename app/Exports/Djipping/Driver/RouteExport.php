<?php

namespace App\Exports\Djipping\Driver;

use App\Route;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RouteExport implements FromView, ShouldAutoSize
{
    private $drivers;

    public function __construct($drivers)
    {
        $this->drivers = $drivers;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $routes = Route::where('subcategory_id', 1)->get();

        foreach ($this->drivers->drivers as $key => $driver) {
            $sumAccept = 0;
            $sumReject = 0;
            $sumRejectAfterAccept = 0;

            foreach ($driver->routes as $route) {
                $sumAccept += $route->accept;
                $sumReject += $route->rejectorder;
                $sumRejectAfterAccept += $route->rejectafteracceptorder;
            }

            $driver->accept = $sumAccept;
            $driver->reject = $sumReject;
            $driver->rejectafteraccept = $sumRejectAfterAccept;
        }

        $totalAccept = 0;
        $totalReject = 0;
        $totalRejectAfterAccept = 0;

        foreach ($this->drivers->total as $endtotal) {
            $totalAccept = $totalAccept + $endtotal->accept;
            $totalReject = $totalReject + $endtotal->reject;
            $totalRejectAfterAccept = $totalRejectAfterAccept + $endtotal->rejectafteraccept;
        }

        return view('excel.djipping.driver.route', [
            'drivers' => $this->drivers->drivers,
            'total' => $this->drivers->total,
            'routes' => $routes,
            'totalaccept' => $totalAccept,
            'totalreject' => $totalReject,
            'totalrejectafteraccept' => $totalRejectAfterAccept,
        ]);
    }
}
