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

class DayExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    use SheetStyle;

    private $report;
    private $managers;
    private $ageCategories;

    /**
     * DayExport constructor.
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
        return 'По дням';
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('excel.age-category.day', [
            'result' => $this->getResult(),
            'managers' => $this->managers,
            'ageCategories' => $this->ageCategories,
            'days' => $this->getDays(),
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
                    Coordinate::stringFromColumnIndex(3) . '3:' . Coordinate::stringFromColumnIndex($maxColumn - 2) . ($row - 2),
                    'outline',
                    Border::BORDER_THICK
                );

                $columnsOfCategory = ($maxColumn - 4) / count($this->ageCategories);

                $i = 3;
                while ($i < $maxColumn - 2) {
                    $this->setBorderStyle(
                        $event,
                        Coordinate::stringFromColumnIndex($i) . '1:' . Coordinate::stringFromColumnIndex($i + $columnsOfCategory - 1) . $row,
                        'outline',
                        Border::BORDER_THICK
                    );

                    $maxCategory = $i + $columnsOfCategory;
                    while ($i < $maxCategory) {
                        $this->setBorderStyle(
                            $event,
                            Coordinate::stringFromColumnIndex($i) . '2:' . Coordinate::stringFromColumnIndex($i) . ($row - 1),
                            'outline',
                            Border::BORDER_THICK
                        );
                        $i += 1;
                    }
                }
            },
        ];
    }

    /**
     * @return array
     */
    private function getDays(): array
    {
        return array_keys(reset(reset($this->report['categories'])['data']['managers'])['dates']);
    }

    /**
     * @param $managerId
     * @param $ageCategoryId
     * @param $day
     * @return int
     */
    private function getValue($managerId, $ageCategoryId, $day): int
    {
        $result = 0;

        foreach ($this->report['categories'] as $category) {
            foreach ($category['data']['managers'] as $manager) {
                if ($manager['id'] === $managerId) {
                    $result += $manager['dates'][$day]['ages'][$ageCategoryId];
                    break;
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
                foreach ($this->getDays() as $day) {
                    if (!array_key_exists('a-' . $key . '_d-' . $day, $result)) {
                        $result['a-' . $key . '_d-' . $day] = 0;
                    }

                    $result['m-' . $manager['id'] . '_a-' . $key . '_d-' . $day] = $this->getValue($manager['id'], $key, $day);

                    $result['m-' . $manager['id']] += ($result['m-' . $manager['id'] . '_a-' . $key . '_d-' . $day] ?? 0);

                    $result['a-' . $key . '_d-' . $day] += ($result['m-' . $manager['id'] . '_a-' . $key . '_d-' . $day] ?? 0);
                }
            }

            $result['amount'] += $result['m-' . $manager['id']];
        }

        return $result;
    }
}
