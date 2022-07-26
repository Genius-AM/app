<?php

namespace App\Core\Reports;

use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class HourRecording
{
    protected $category;
    protected $subcategories = [];
    protected $managers = [];
    protected $companies = [];
    protected $start = null;
    protected $end = null;
    protected $array = [];
    protected $hoursPeriods = [];

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
            [Carbon::createFromTime(7), Carbon::createFromTime(7, 59, 59)],
            [Carbon::createFromTime(8), Carbon::createFromTime(8, 59, 59)],
            [Carbon::createFromTime(9), Carbon::createFromTime(9, 59, 59)],
            [Carbon::createFromTime(10), Carbon::createFromTime(10, 59, 59)],
            [Carbon::createFromTime(11), Carbon::createFromTime(11, 59, 59)],
            [Carbon::createFromTime(12), Carbon::createFromTime(12, 59, 59)],
            [Carbon::createFromTime(13), Carbon::createFromTime(13, 59, 59)],
            [Carbon::createFromTime(14), Carbon::createFromTime(14, 59, 59)],
            [Carbon::createFromTime(15), Carbon::createFromTime(15, 59, 59)],
            [Carbon::createFromTime(16), Carbon::createFromTime(16, 59, 59)],
            [Carbon::createFromTime(17), Carbon::createFromTime(17, 59, 59)],
            [Carbon::createFromTime(18), Carbon::createFromTime(18, 59, 59)],
            [Carbon::createFromTime(19), Carbon::createFromTime(19, 59, 59)],
            [Carbon::createFromTime(20), Carbon::createFromTime(20, 59, 59)],
            [Carbon::createFromTime(21), Carbon::createFromTime(21, 59, 59)],
            [Carbon::createFromTime(22), Carbon::createFromTime(22, 59, 59)],
            [Carbon::createFromTime(23), Carbon::createFromTime(23, 59, 59)],
            [Carbon::createFromTime(), Carbon::createFromTime(0, 59, 59)],
            [Carbon::createFromTime(1), Carbon::createFromTime(1, 59, 59)],
            [Carbon::createFromTime(2), Carbon::createFromTime(2, 59, 59)],
            [Carbon::createFromTime(3), Carbon::createFromTime(3, 59, 59)],
            [Carbon::createFromTime(4), Carbon::createFromTime(4, 59, 59)],
            [Carbon::createFromTime(5), Carbon::createFromTime(5, 59, 59)],
            [Carbon::createFromTime(6), Carbon::createFromTime(6, 59, 59)],
        ];
        $this->array['period'] = $this->getStartDate()->format('d.m') . ' - ' . $this->getEndDate()->format('d.m');
        foreach ($this->hoursPeriods as $hourPeriod) {
            $this->array['periods'][] = $hourPeriod[0]->format('H');
        }
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
        foreach ($this->hoursPeriods as $hourPeriod) {
            $arr['hours'][$hourPeriod[0]->format('H')] = 0;
        }
    }

    /**
     * @param array $arr
     * @param Order $order
     */
    private function setValue(array &$arr, Order $order): void
    {
        $hour = $order->created_at->format('H');

        foreach ($order->age_categories as $ageCategory) {
            $arr['hours'][$hour] += $ageCategory->pivot->amount;
        }
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $this->setDefaultAmount($this->array);

        $managers = $this->getManagers();

        foreach ($managers as $manager) {
            $this->array['managers'][$manager->id]['name'] = $manager->name;

            $this->setDefaultAmount($this->array['managers'][$manager->id]);

            $orders = $this->getOrders($manager->id);

            foreach ($orders as $order) {
                $this->setValue($this->array['managers'][$manager->id], $order);

                $this->setValue($this->array, $order);
            }
        }

        return $this->array;
    }
}