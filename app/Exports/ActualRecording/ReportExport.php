<?php

namespace App\Exports\ActualRecording;

use App\Exports\ActualRecording\Sheets\BaseExport;
use App\Exports\ActualRecording\Sheets\DayExport;
use App\Exports\ActualRecording\Sheets\HourExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements WithMultipleSheets
{
    private $report;
    private $managers;
    private $categories;
    private $days;
    private $hours;

    /**
     * ReportExport constructor.
     * @param $report
     * @param $managers
     * @param $categories
     * @param $days
     * @param $hours
     */
    public function __construct($report, $managers, $categories, $days, $hours)
    {
        $this->report = $report;
        $this->managers = $managers;
        $this->categories = $categories;
        $this->days = $days;
        $this->hours = $hours;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [
            new BaseExport($this->report, $this->managers, $this->categories),
        ];

        if ($this->days) {
            $sheets[] = new DayExport($this->report, $this->managers, $this->categories);
        }

        if ($this->hours) {
            $sheets[] = new HourExport($this->report, $this->managers, $this->categories);
        }

        return $sheets;
    }
}