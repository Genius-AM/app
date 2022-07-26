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

class DayExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    use SheetStyle;

    private $report;
    private $managers;
    private $categories;

    /**
     * Day constructor.
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
        return 'По дням';
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('excel.actual-recording.day', [
            'result' => $this->getResult(),
            'managers' => $this->managers,
            'categories' => $this->categories,
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
                    Coordinate::stringFromColumnIndex(4) . '1:' . Coordinate::stringFromColumnIndex($maxColumn - 3) . $row,
                    'outline',
                    Border::BORDER_THICK
                );

                $this->setBorderStyle(
                    $event,
                    Coordinate::stringFromColumnIndex(4) . '3:' . Coordinate::stringFromColumnIndex($maxColumn - 3) . ($row - 2),
                    'outline',
                    Border::BORDER_THICK
                );

                $columnsOfCategory = count($this->getDays()) * 2;

                $i = 4;
                while ($i < $maxColumn - 3) {
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
                            Coordinate::stringFromColumnIndex($i) . '2:' . Coordinate::stringFromColumnIndex($i + 1) . ($row - 1),
                            'outline',
                            Border::BORDER_THICK
                        );
                        $i += 2;
                    }
                }

                $i = 4;
                while ($i < $maxColumn - 1) {
                    $columnName = Coordinate::stringFromColumnIndex($i);
                    $this->setFontColor(
                        $event,
                        $columnName . '3:' . $columnName . ($row - 2),
                        '0000FF'
                    );
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setAutoSize(false);
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setWidth(5);

                    $columnName = Coordinate::stringFromColumnIndex($i + 1);
                    $this->setFontColor(
                        $event,
                        $columnName . '3:' . $columnName . ($row - 2),
                        'FF0000'
                    );
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setAutoSize(false);
                    $event->getSheet()->getDelegate()->getColumnDimension($columnName)->setWidth(5);

                    $i += 2;
                }
            },
        ];
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
     * @return array
     */
    private function getDays(): array
    {
        return reset($this->report)['data']['days'];
    }

    /**
     * @param $managerId
     * @return array
     */
    private function getRoutes($managerId): array
    {
        $routes = [];

        foreach ($this->report as $category) {
            foreach ($category['data']['managers'] as $manager) {
                if ($manager['id'] === $managerId) {
                    if (array_key_exists('routes', $manager)) {
                        foreach ($manager['routes'] as $route) {
                            $routes[] = [
                                'id' => $route['id'],
                                'name' => $route['name'],
                                'category_id' => $route['category_id'],
                                'accept' => $route['accept'],
                                'reject' => $route['reject'],
                                'days' => $route['days'],
                            ];
                        }
                    }
                    break;
                }
            }
        }

        return $routes;
    }

    /**
     * @return int[]
     */
    private function getSumma(): array
    {
        $result = $this->getDefaultValue();

        foreach ($this->report as $category) {
            foreach ($category['data']['managers'] as $manager) {
                if (array_key_exists('routes', $manager)) {
                    foreach ($manager['routes'] as $route) {
                        $result['accept'] += $route['accept'];
                        $result['reject'] += $route['reject'];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array $arr
     * @param string $key
     * @param array $value
     */
    private function addValue(array &$arr, string $key, array $value): void
    {
        if (!array_key_exists($key, $arr)) {
            $arr[$key] = $this->getDefaultValue();
        }

        $arr[$key]['accept'] += $value['accept'];
        $arr[$key]['reject'] += $value['reject'];
    }

    /**
     * @return int[]
     */
    private function getResult(): array
    {
        $result = $this->getSumma();

        foreach ($this->managers as &$manager) {
            foreach ($this->categories as $category) {
                foreach ($this->getDays() as $day) {
                    $result['m-' . $manager['id'] . '_c-' . $category['id'] . '_d-' . $day] = $this->getDefaultValue();
                    if (!array_key_exists('c-' . $category['id'] . '_d-' . $day, $result)) {
                        $result['c-' . $category['id'] . '_d-' . $day] = $this->getDefaultValue();
                    }
                }
            }
            $result['m-' . $manager['id']] = $this->getDefaultValue();

            $manager['routes'] = $this->getRoutes($manager['id']);
            foreach ($manager['routes'] as $route) {
                foreach ($this->categories as $category) {
                    foreach ($this->getDays() as $day) {
                        $value = [
                            'accept' => $route['days'][$day]['accept'],
                            'reject' => $route['days'][$day]['reject'],
                        ];

                        $result['m-' . $manager['id'] . '_r-' . $route['id'] . '_c-' . $category['id'] . '_d-' . $day] = $value;

                        if ($category['id'] != $route['category_id']) {
                            $value = $this->getDefaultValue();
                        }
                        $this->addValue($result, 'm-' . $manager['id'] . '_c-' . $category['id'] . '_d-' . $day, $value);
                        $this->addValue($result, 'c-' . $category['id'] . '_d-' . $day, $value);
                    }
                }

                $this->addValue($result, 'm-' . $manager['id'], $route);
            }
        }

        return $result;
    }
}
