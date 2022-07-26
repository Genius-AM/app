<?php

namespace App\Core\Reports;

use App\Order;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AgeCategory
{
    protected $category;
    protected $subcategories = [];
    protected $managers = [];
    protected $companies = [];
    protected $start = null;
    protected $end = null;
    protected $array = [];
    protected $hoursPeriods = [];
    protected $ageCategories = [];
    protected $datesPeriods = [];

    /**
     * AgeCategory constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->category = $params['category'];
        $this->subcategories = array_key_exists('subcategories', $params) ? array_filter($params['subcategories']) : [];
        $this->managers = array_key_exists('managers', $params) ? $params['managers'] : [];
        $this->companies = array_key_exists('companies', $params) ? $params['companies'] : [];
        $this->start = array_key_exists('start', $params) ? $params['start'] : null;
        $this->end = array_key_exists('end', $params) ? $params['end'] : null;
        $this->hoursPeriods = [
            [Carbon::createFromTime(8), Carbon::createFromTime(9, 59, 59)],
            [Carbon::createFromTime(10), Carbon::createFromTime(11, 59, 59)],
            [Carbon::createFromTime(12), Carbon::createFromTime(13, 59, 59)],
            [Carbon::createFromTime(14), Carbon::createFromTime(15, 59, 59)],
            [Carbon::createFromTime(16), Carbon::createFromTime(17, 59, 59)],
            [Carbon::createFromTime(18), Carbon::createFromTime(19, 59, 59)],
            [Carbon::createFromTime(20), Carbon::createFromTime(21, 59, 59)],
            [Carbon::createFromTime(22), Carbon::createFromTime(23, 59, 59), Carbon::createFromTime(), Carbon::createFromTime(07, 59, 59)],
        ];
        $this->ageCategories = $this->getAgeCategories();
        $this->datesPeriods = $this->getPeriod();
        $this->array['age_categories'] = $this->ageCategories->pluck('name', 'id')->toArray();
    }

    /**
     * @param array $params
     * @return static
     */
    public static function create(array $params): self
    {
        return new self($params);
    }

    /**
     * @return array|BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getManagers()
    {
        return User::where('role_id', 1)
            ->when($this->managers, function (Builder $query, $manager) {
                return $query->whereIn('id', $manager);
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getAgeCategories()
    {
        return \App\Models\AgeCategory::query()
            ->orderBy('from')
            ->orderBy('to')
            ->get();
    }

    /**
     * @param int $manager
     * @return Order[]|array|BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     */
    private function getOrders(int $manager)
    {
        return Order::query()
            ->has('age_categories')
            ->where('category_id', $this->category)
            ->where('manager_id', $manager)
            ->when($this->subcategories, function (Builder $query, $subcategories) {
                $query->whereIn('subcategory_id', $subcategories);
            })
            ->when($this->companies, function (Builder $query, $companies) {
                return $query->whereIn('company_id', $companies);
            })
            ->when($this->start, function (Builder $query, $start) {
                return $query->where('created_at', '>=', Carbon::createFromFormat('Y-m-d', $start)->startOfDay());
            })
            ->when($this->end, function (Builder $query, $end) {
                return $query->where('created_at', '<=', Carbon::createFromFormat('Y-m-d', $end)->endOfDay());
            })
            ->orderBy('created_at')
            ->get();
    }

    /**
     * @param array $arr
     * @param Order $order
     */
    private function setDays(array &$arr, Order $order): void
    {
        $day = $order->created_at->format('Y-m-d');

        $this->setValue($arr['dates'][$day], $order->age_categories);

        $this->setHours($arr['dates'][$day], $order);
    }

    /**
     * @param array $arr
     * @param Order $order
     */
    private function setHours(array &$arr, Order $order): void
    {
        $hour = Carbon::createFromFormat('H:i:s', $order->created_at->format('H:i:s'));

        foreach ($this->hoursPeriods as $hoursPeriod) {
            if ($period = $this->hasPeriod($hoursPeriod, $hour)) {
                $this->setValue($arr['times'][$period], $order->age_categories);
                break;
            }
        }
    }

    /**
     * @param array $arr
     * @param $ageCategories
     */
    private function setValue(array &$arr, $ageCategories): void
    {
        foreach ($ageCategories as $ageCategory) {
            $arr['ages'][$ageCategory->id] += $ageCategory->pivot->amount;
            $arr['ages']['amount'] += $ageCategory->pivot->amount;
        }
    }

    /**
     * @param array $period
     * @param Carbon $time
     * @return false|string
     */
    private function hasPeriod(array $period, Carbon $time)
    {
        if (
            count($period) == 2 &&
            $time >= $period[0] && $time <= $period[1]
        ) {
            return $period[0]->hour . '-' . $period[1]->hour;
        }

        if (
            count($period) == 4 &&
            (($time >= $period[0] && $time <= $period[1]) || ($time >= $period[2] && $time <= $period[3]))
        ) {
            return $period[0]->hour . '-' . $period[3]->hour;
        }

        return false;
    }

    /**
     * @return CarbonPeriod
     */
    private function getPeriod(): CarbonPeriod
    {
        return CarbonPeriod::create($this->getStartDate(), $this->getEndDate());
    }

    /**
     * @return Carbon
     */
    private function getStartDate(): Carbon
    {
        if (!$this->start) {
            $firstOrder = Order::query()
                ->where('category_id', $this->category)
                ->orderBy('created_at')
                ->first();

            if ($firstOrder) {
                return $firstOrder->created_at;
            }

            return Carbon::now();
        }

        return Carbon::parse($this->start);
    }

    /**
     * @return Carbon
     */
    private function getEndDate(): Carbon
    {
        if (!$this->end) {
            return Carbon::now();
        }

        return Carbon::parse($this->end);
    }

    /**
     * @param array $arr
     */
    private function setDefaultAmount(array &$arr): void
    {
        foreach ($this->ageCategories as $ageCategory) {
            $arr['ages'][$ageCategory->id] = 0;
            $arr['ages']['amount'] = 0;

            foreach ($this->datesPeriods as $datesPeriod) {
                $arr['dates'][$datesPeriod->format('Y-m-d')]['ages'][$ageCategory->id] = 0;
                $arr['dates'][$datesPeriod->format('Y-m-d')]['ages']['amount'] = 0;

                foreach ($this->hoursPeriods as $hoursPeriod) {
                    $periodName = $this->getPeriodName($hoursPeriod);

                    $arr['dates'][$datesPeriod->format('Y-m-d')]['times'][$periodName]['ages'][$ageCategory->id] = 0;
                    $arr['dates'][$datesPeriod->format('Y-m-d')]['times'][$periodName]['ages']['amount'] = 0;
                }
            }
        }
    }

    /**
     * @param array $period
     * @return string
     */
    private function getPeriodName(array $period): string
    {
        if (count($period) == 4) {
            return $period[0]->hour . '-' . $period[3]->hour;
        }

        return $period[0]->hour . '-' . $period[1]->hour;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $managers = $this->getManagers();

        foreach ($this->ageCategories as $ageCategory) {
            $this->array['ages'][$ageCategory->id] = 0;
        }
        $this->array['ages']['amount'] = 0;

        foreach ($managers as $key => $manager) {
            $this->array['managers'][$key]['id'] = $manager->id;
            $this->array['managers'][$key]['name'] = $manager->name;

            $this->setDefaultAmount($this->array['managers'][$key]);

            $orders = $this->getOrders($manager->id);

            foreach ($orders as $order) {
                $this->setValue($this->array['managers'][$key], $order->age_categories);

                $this->setValue($this->array, $order->age_categories);

                $this->setDays($this->array['managers'][$key], $order);
            }
        }

        return $this->array;
    }
}