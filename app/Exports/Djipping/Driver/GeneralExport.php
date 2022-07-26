<?php

namespace App\Exports\Djipping\Driver;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GeneralExport implements FromView, ShouldAutoSize
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
        return view('excel.djipping.driver.general', [
            'drivers' => $this->drivers
        ]);
    }
}
