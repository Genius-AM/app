<?php

namespace App\Exports\ActualRecording\Sheets;

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
    private $categories;

    /**
     * Base constructor.
     * @param $report
     * @param $managers
     * @param $categories
     */
    public function __construct($report, $managers, $categories)
    {
        $this->report = $report;
        $this->managers = $managers;
        $this->categories = $categories;
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
        return view('excel.actual-recording.base', [
            'result' => $this->getResult(),
            'managers' => $this->managers,
            'categories' => $this->categories,
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
                    Coordinate::stringFromColumnIndex(3) . '1:' . Coordinate::stringFromColumnIndex($maxColumn - 3) . $row,
                    'outline',
                    Border::BORDER_THICK
                );

                $this->setBorderStyle(
                    $event,
                    Coordinate::stringFromColumnIndex(3) . '2:' . Coordinate::stringFromColumnIndex($maxColumn - 3) . ($row - 1),
                    'outline',
                    Border::BORDER_THICK
                );

                $columnsOfCategory = ($maxColumn - 5) / count($this->categories);

                $i = 3;
                while ($i < $maxColumn - 3) {
                    $this->setBorderStyle(
                        $event,
                        Coordinate::stringFromColumnIndex($i) . '1:' . Coordinate::stringFromColumnIndex($i + $columnsOfCategory - 1) . $row,
                        'outline',
                        Border::BORDER_THICK
                    );
                    $i += $columnsOfCategory;
                }

                $i = 3;
                while ($i < $maxColumn - 1) {
                    $columnName = Coordinate::stringFromColumnIndex($i);
                    $this->setFontColor(
                        $event,
                        $columnName . '2:' . $columnName . ($row - 1),
                        '0000FF'
                    );
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setAutoSize(false);
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setWidth(5);

                    $columnName = Coordinate::stringFromColumnIndex($i + 1);
                    $this->setFontColor(
                        $event,
                        $columnName . '2:' . $columnName . ($row - 1),
                        'FF0000'
                    );
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setAutoSize(false);
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setWidth(5);

                    $i += $columnsOfCategory;
                }
            },
        ];
    }

    /**
     * @param $managerId
     * @param $categoryId
     * @return array
     */
    private function getValue($managerId, $categoryId): array
    {
        $accept = null;
        $reject = null;

        foreach ($this->report as $category) {
            if ($category['id'] === $categoryId) {
                foreach ($category['data']['managers'] as $manager) {
                    if ($manager['id'] === $managerId) {
                        $accept = 0;
                        $reject = 0;

                        if (array_key_exists('routes', $manager)) {
                            foreach ($manager['routes'] as $route) {
                                $accept += $route['accept'];
                                $reject += $route['reject'];
                            }
                        }
                        break;
                    }
                }
                break;
            }
        }

        return compact('accept', 'reject');
    }

    /**
     * @return int[]
     */
    private function getDefaultValue(): array
    {
        return [
            'accept' => 0,
            'reject' => 0
        ];
    }

    /**
     * @return int[]
     */
    private function getResult(): array
    {
        $result = $this->getDefaultValue();

        foreach ($this->managers as $manager) {
            $result[$manager['id']] = $this->getDefaultValue();

            foreach ($this->categories as $category) {
                if (!array_key_exists($category['id'], $result)) {
                    $result[$category['id']] = $this->getDefaultValue();
                }

                $result[$manager['id'] . '_' . $category['id']] = $this->getValue($manager['id'], $category['id']);

                $result[$manager['id']]['accept'] += ($result[$manager['id'] . '_' . $category['id']]['accept'] ?? 0);
                $result[$manager['id']]['reject'] += ($result[$manager['id'] . '_' . $category['id']]['reject'] ?? 0);

                $result[$category['id']]['accept'] += ($result[$manager['id'] . '_' . $category['id']]['accept'] ?? 0);
                $result[$category['id']]['reject'] += ($result[$manager['id'] . '_' . $category['id']]['reject'] ?? 0);
            }

            $result['accept'] += $result[$manager['id']]['accept'];
            $result['reject'] += $result[$manager['id']]['reject'];
        }

        return $result;
    }
}
