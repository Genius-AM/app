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

class HourExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    use SheetStyle;

    private $report;
    private $managers;
    private $ageCategories;

    /**
     * HourExport constructor.
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
        return 'По часам';
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('excel.age-category.hour', [
            'result' => $this->getResult(),
            'managers' => $this->managers,
            'ageCategories' => $this->ageCategories,
            'days' => $this->getDays(),
            'hours' => $this->getHours(),
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
                    Coordinate::stringFromColumnIndex(3) . '4:' . Coordinate::stringFromColumnIndex($maxColumn - 2) . ($row - 3),
                    'outline',
                    Border::BORDER_THICK
                );

                $columnsOfCategory = ($maxColumn - 4) / count($this->ageCategories);
                $columnsOfDay = $columnsOfCategory / count($this->getDays());

                $i = 3;
                while ($i < $maxColumn - 2) {
                    $this->setBorderStyle(
                        $event,
                        Coordinate::stringFromColumnIndex($i) . '1:' . Coordinate::stringFromColumnIndex($i + $columnsOfCategory - 1) . $row,
                        'outline',
                        Border::BORDER_THICK
                    );

                    $max = $i + $columnsOfCategory;
                    while ($i < $max) {
                        $this->setBorderStyle(
                            $event,
                            Coordinate::stringFromColumnIndex($i) . '2:' . Coordinate::stringFromColumnIndex($i + $columnsOfDay - 1) . ($row - 1),
                            'outline',
                            Border::BORDER_THICK
                        );

                        $maxDays = $i + $columnsOfDay;
                        while ($i < $maxDays) {
                            $this->setBorderStyle(
                                $event,
                                Coordinate::stringFromColumnIndex($i) . '3:' . Coordinate::stringFromColumnIndex($i) . '3',
                                'outline',
                                Border::BORDER_THICK
                            );
                            $this->setBorderStyle(
                                $event,
                                Coordinate::stringFromColumnIndex($i) . ($row - 2) . ':' . Coordinate::stringFromColumnIndex($i) . ($row - 2),
                                'outline',
                                Border::BORDER_THICK
                            );
                            $i += 1;
                        }
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
     * @return array
     */
    private function getHours(): array
    {
        return array_keys(reset(reset(reset($this->report['categories'])['data']['managers'])['dates'])['times']);
    }

    /**
     * @param $managerId
     * @param $ageCategoryId
     * @param $day
     * @param $time
     * @return int
     */
    private function getValue($managerId, $ageCategoryId, $day, $time): int
    {
        $result = 0;

        foreach ($this->report['categories'] as $category) {
            foreach ($category['data']['managers'] as $manager) {
                if ($manager['id'] === $managerId) {
                    $result += $manager['dates'][$day]['times'][$time]['ages'][$ageCategoryId];
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
                    foreach ($this->getHours() as $hour) {
                        if (!array_key_exists('a-' . $key . '_d-' . $day . '_h-' . $hour, $result)) {
                            $result['a-' . $key . '_d-' . $day . '_h-' . $hour] = 0;
                        }

                        $result['m-' . $manager['id'] . '_a-' . $key . '_d-' . $day . '_h-' . $hour] = $this->getValue($manager['id'], $key, $day, $hour);

                        $result['m-' . $manager['id']] += ($result['m-' . $manager['id'] . '_a-' . $key . '_d-' . $day . '_h-' . $hour] ?? 0);

                        $result['a-' . $key . '_d-' . $day . '_h-' . $hour] += ($result['m-' . $manager['id'] . '_a-' . $key . '_d-' . $day . '_h-' . $hour] ?? 0);
                    }
                }
            }

            $result['amount'] += $result['m-' . $manager['id']];
        }

        return $result;
    }
}
