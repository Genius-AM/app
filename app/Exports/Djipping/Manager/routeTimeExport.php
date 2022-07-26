<?php

namespace App\Exports\Djipping\Manager;

use App\Core\Route\RouteOptions;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class routeTimeExport implements FromView, ShouldAutoSize
{
    private $managers;
    private $times;

    public function __construct($managers)
    {
        $this->managers = $managers;

        $this->times = RouteOptions::getTimesForRoutes();
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('excel.djipping.manager.route-time', [
            'managers' => $this->managers,
            'times' => $this->times,
        ]);
    }
}
