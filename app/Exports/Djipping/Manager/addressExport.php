<?php

namespace App\Exports\Djipping\Manager;

use App\Route;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class addressExport implements FromView, ShouldAutoSize
{
    private $addresses;

    public function __construct($addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $number = 0;
        foreach ($this->addresses as $address) {
            $count = 0;
            $number++;
            foreach ($address->managers as $manager) {
                $count += $manager->total;
            }
            $manager->number = $number;
            $address->total = $count;
        }

        return view('excel.djipping.manager.address', [
            'addresses' => $this->addresses,
        ]);
    }
}
