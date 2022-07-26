<?php

namespace App\Exports\DeletedOrder;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GeneralExport implements FromView, ShouldAutoSize
{
    private $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('excel.deleted-orders.general', [
            'orders' => $this->orders
        ]);
    }
}
