<?php

namespace App\Exports\AgeCategory\Sheets;

use App\Exports\SheetStyle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class GlobalExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    use SheetStyle;

    private $report;

    /**
     * GlobalExport constructor.
     * @param $report
     */
    public function __construct($report)
    {
        $this->report = $report;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Общий';
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('excel.age-category.global', [
            'result' => $this->getResult(),
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

                $this->setBorderStyle($event, 'A2:' . $column . $row, 'allBorders', Border::BORDER_THIN);

                $styleArray = [
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $event->getSheet()->getDelegate()->getStyle('A1')->applyFromArray($styleArray);

                $maxColumn = Coordinate::columnIndexFromString($column);

                $i = 3;
                while ($i <= $maxColumn) {
                    $columnName = Coordinate::stringFromColumnIndex($i);
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setAutoSize(false);
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setWidth(5);
                    $i++;
                }
            },
        ];
    }

    /**
     * @return array
     */
    private function getResult(): array
    {
        $result = array_shift($this->report);

        foreach ($this->report as $item) {
            foreach ($item['hours'] as $hour => $value) {
                $result['hours'][$hour] += $value;
            }

            foreach ($item['managers'] as $id => $manager) {
                if (!array_key_exists($id, $result['managers'])) {
                    $result['managers'][$id] = $manager;
                } else {
                    foreach ($item['managers'][$id]['hours'] as $hour => $value) {
                        $result['managers'][$id]['hours'][$hour] += $value;
                    }
                }
            }
        }

        $result['all'] = array_sum($result['hours']);

        foreach ($result['managers'] as $id => $manager) {
            $result['managers'][$id]['all'] = array_sum($result['managers'][$id]['hours']);
        }

        return $result;
    }
}
