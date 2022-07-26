<?php

namespace App\Exports\AgeCategory;

use App\Exports\AgeCategory\Sheets\BaseExport;
use App\Exports\AgeCategory\Sheets\DayExport;
use App\Exports\AgeCategory\Sheets\GlobalExport;
use App\Exports\AgeCategory\Sheets\HourExport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements WithMultipleSheets
{
    private $report;
    private $days;
    private $hours;
    private $managers;
    private $ageCategories;

    /**
     * ReportExport constructor.
     * @param $report
     * @param $days
     * @param $hours
     */
    public function __construct($report, $days, $hours)
    {
        $this->report = $report;
        $this->days = $days;
        $this->hours = $hours;
        $this->managers = $this->getManagers();
        $this->ageCategories = $this->getAgeCategories();
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [
            new BaseExport($this->report, $this->managers, $this->ageCategories)
        ];

        if ($this->days) {
            $sheets[] = new DayExport($this->report, $this->managers, $this->ageCategories);
        }

        if ($this->hours) {
            $sheets[] = new HourExport($this->report, $this->managers, $this->ageCategories);
        }

        $sheets[] = new GlobalExport($this->report['global']);

        return $sheets;
    }

    /**
     * @return array
     */
    private function getAgeCategories(): array
    {
        return $this->report['categories'][0]['data']['age_categories'];
    }

    /**
     * @return array
     */
    private function getManagers(): array
    {
        $managers = [];

        foreach($this->report['categories'] as $item) {
            foreach ($item['data']['managers'] as $manager) {
                $managers[] = [
                    'id' => $manager['id'],
                    'name' => $manager['name'],
                ];
            }
        }

        $managers = Collection::make($managers);

        $managers = $managers->unique(function ($item) {
            return $item['name'].$item['id'];
        });

        return $managers->sortBy('name')->values()->toArray();
    }
}