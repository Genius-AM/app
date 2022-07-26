<?php

namespace App\Exports\Sea\Manager;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrdersExport implements FromView, ShouldAutoSize
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
        return view('excel.sea.manager.orders', [
            'orders' => $this->orders
        ]);
    }
}
