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
use PhpOffice\PhpSpreadsheet\Style\Border;

class BaseExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    use SheetStyle;

    private $report;
    private $managers;
    private $ageCategories;

    /**
     * BaseExport constructor.
     * @param $report
     * @param $managers
     * @param $ageCategories
     */
    public function __construct($report, $managers, $ageCategories)
    {
        $this->report = $report;
        $this->managers = $managers;
        $this->ageCategories = $ageCategories;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Основной';
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('excel.age-category.base', [
            'result' => $this->getResult(),
            'managers' => $this->managers,
            'ageCategories' => $this->ageCategories,
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

                $maxColumn = Coordinate::columnIndexFromString($column);

                $this->setBorderStyle(
                    $event,
                    Coordinate::stringFromColumnIndex(3) . '2:' . Coordinate::stringFromColumnIndex($maxColumn - 2) . ($row - 1),
                    'outline',
                    Border::BORDER_THICK
                );

                $this->setBorderStyle(
                    $event,
                    Coordinate::stringFromColumnIndex(3) . '1:' . Coordinate::stringFromColumnIndex($maxColumn - 2) . $row,
                    'outline',
                    Border::BORDER_THICK
                );
            },
        ];
    }

    /**
     * @param $managerId
     * @param $ageCategoryId
     * @return int
     */
    private function getValue($managerId, $ageCategoryId): int
    {
        $result = 0;

        foreach ($this->report['categories'] as $category) {
            foreach ($category['data']['managers'] as $manager) {
                if ($manager['id'] === $managerId) {
                    $result += $manager['ages'][$ageCategoryId];
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getResult(): array
    {
        $result['amount'] = 0;

        foreach ($this->managers as $manager) {
            $result['m-' . $manager['id']] = 0;

            foreach ($this->ageCategories as $key => $category) {
                if (!array_key_exists('a-' . $key, $result)) {
                    $result['a-' . $key] = 0;
                }

                $result['m-' . $manager['id'] . '_a-' . $key] = $this->getValue($manager['id'], $key);

                $result['m-' . $manager['id']] += ($result['m-' . $manager['id'] . '_a-' . $key] ?? 0);

                $result['a-' . $key] += ($result['m-' . $manager['id'] . '_a-' . $key] ?? 0);
            }

            $result['amount'] += $result['m-' . $manager['id']];
        }

        return $result;
    }
}
