<?php

namespace App\Exports\Sea\Manager;

use App\Exports\SheetStyle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AdultsChildrenExport implements FromView, ShouldAutoSize, WithEvents
{
    use SheetStyle;

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
        $adults = array_reduce($this->orders, function ($accumulator, $item) {
            return $accumulator + $item['adults'];
        }, 0);

        $children = array_reduce($this->orders, function ($accumulator, $item) {
            return $accumulator + $item['children'];
        }, 0);

        return view('excel.sea.manager.adults-children', [
            'orders' => $this->orders,
            'adults' => $adults,
            'children' => $children,
        ]);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $column = $event->getSheet()->getDelegate()->getHighestColumn();
                $row = $event->getSheet()->getDelegate()->getHighestRow();

                $this->setBorderStyle($event, 'A1:' . $column . $row, 'allBorders', Border::BORDER_THIN);
            },
        ];
    }
}
