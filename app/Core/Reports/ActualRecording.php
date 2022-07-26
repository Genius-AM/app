<?php

namespace App\Core\Reports;

use App\Category;
use App\Order;
use App\Route;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;

class ActualRecording
{
    protected $category;
    protected $subcategories = [];
    protected $managers = [];
    protected $companies = [];
    protected $start = null;
    protected $end = null;
    protected $days = false;
    protected $hours = false;
    protected $array = [];
    protected $hoursPeriods = [];

    /**
     * ActualRecording constructor.
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
        $this->days = array_key_exists('days', $params) ? $params['days'] : false;
        $this->hours = array_key_exists('hours', $params) ? $params['hours'] : false;
        $this->array['total'] = [];
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
     * @return array|\Illuminate\Database\Concerns\BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getRoutes()
    {
        return Route::where('category_id', $this->category)
            ->when($this->subcategories, function (Builder $query, $subcategories) {
                $query->whereIn('subcategory_id', $subcategories);
            })
            ->when($this->category == Category::QUADBIKE, function (Builder $query) {
                $query->whereNotIn('name', ['Дельфин', 'Новорос', 'Дюрсо']);
            })
            ->get();
    }

    /**
     * @return array|\Illuminate\Database\Concerns\BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection
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
     * @param int $route
     * @param int $manager
     * @param bool $accept
     * @return Order[]|array|\Illuminate\Database\Concerns\BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    private function getOrders(int $route, int $manager, bool $accept = true)
    {
        return Order::query()
            ->where('category_id', $this->category)
            ->where('manager_id', $manager)
            ->where('route_id', $route)
            ->when($this->companies, function (Builder $query, $companies) {
                return $query->whereIn('company_id', $companies);
            })
            ->when($accept, function (Builder $query) {
                return $query->whereNotIn('status_id', [7]);
            })
            ->when(!$accept, function (Builder $query) {
                return $query->whereIn('status_id', [5])
                    ->whereNotIn('refuser_id', User::where('role_id', 3)->pluck('id'));
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
     * @param $acceptOrders
     * @param $rejectOrders
     * @return array
     */
    private function getDays($acceptOrders, $rejectOrders): array
    {
        $days = [];

        $this->setDays($days, $acceptOrders);

        $this->setDays($days, $rejectOrders, false);

        return $days;
    }

    /**
     * @param array $arr
     * @param $orders
     * @param bool $accept
     */
    private function setDays(array &$arr, $orders, bool $accept = true): void
    {
        if ($this->days) {
            foreach ($orders as $order) {
                $day = $order->created_at->format('Y-m-d');

                $this->setValue($arr, $day, $order->people_sum, $accept);

                $this->setHours($arr[$day], $order, $accept);
            }
        }
    }

    /**
     * @param array $arr
     * @param Order $order
     * @param bool $accept
     */
    private function setHours(array &$arr, Order $order, bool $accept = true): void
    {
        if ($this->hours) {
            $hour = Carbon::createFromFormat('H:i:s', $order->created_at->format('H:i:s'));
            foreach ($this->hoursPeriods as $hoursPeriod) {
                if ($period = $this->hasPeriod($hoursPeriod, $hour)) {
                    if (!array_key_exists('times', $arr)) {
                        $arr['times'] = [];
                    }
                    $this->setValue($arr['times'], $period, $order->people_sum, $accept);
                    break;
                }
            }
        }
    }

    /**
     * @param array $arr
     * @param string $key
     * @param int $value
     * @param bool $accept
     */
    private function setValue(array &$arr, string $key, int $value, bool $accept = true): void
    {
        if (isset($arr[$key])) {
            $arr[$key][$accept ? 'accept' : 'reject'] = $arr[$key][$accept ? 'accept' : 'reject'] + $value;
        } else {
            $arr[$key][$accept ? 'accept' : 'reject'] = $value;
            if (!array_key_exists(!$accept ? 'accept' : 'reject', $arr[$key])) {
                $arr[$key][!$accept ? 'accept' : 'reject'] = 0;
            }
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
     * @return void
     */
    private function correctArray(): void
    {
        if ($this->days) {
            foreach ($this->array['managers'] as &$manager) {
                if (array_key_exists('routes', $manager)) {
                    foreach ($manager['routes'] as &$route) {
                        foreach ($this->getPeriod() as $date) {
                            if (!array_key_exists($date->format('Y-m-d'), $route['days'])) {
                                $route['days'][$date->format('Y-m-d')] = [
                                    'accept' => 0,
                                    'reject' => 0,
                                ];
                            }

                            if (!array_key_exists('times', $route['days'][$date->format('Y-m-d')])) {
                                $route['days'][$date->format('Y-m-d')]['times'] = [];
                            }
                            foreach ($this->hoursPeriods as $hoursPeriod) {
                                $periodName = $this->getPeriodName($hoursPeriod);

                                if (!array_key_exists($periodName, $route['days'][$date->format('Y-m-d')]['times'])) {
                                    $route['days'][$date->format('Y-m-d')]['times'][$periodName] = [
                                        'accept' => 0,
                                        'reject' => 0,
                                    ];
                                }
                            }
                        }
                        ksort($route['days']);
                    }
                }
            }
        }
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
     * @param int $key
     * @param Route $route
     * @param array $days
     * @param int $accept
     * @param int $reject
     */
    private function setRoute(int $key, Route $route, array $days, int $accept, int $reject): void
    {
        $this->array['managers'][$key]['routes'][$route->id]['id'] = $route->id;
        $this->array['managers'][$key]['routes'][$route->id]['name'] = $route->name;
        $this->array['managers'][$key]['routes'][$route->id]['color'] = $route->color;
        $this->array['managers'][$key]['routes'][$route->id]['category_id'] = $route->category_id;
        $this->array['managers'][$key]['routes'][$route->id]['accept'] = $accept;
        $this->array['managers'][$key]['routes'][$route->id]['reject'] = $reject;
        $this->array['managers'][$key]['routes'][$route->id]['days'] = $days;

        $this->setTotal($this->array['total'], $route->id, $accept, $reject);
    }

    /**
     * @param array $total
     * @param int $route
     * @param int $accept
     * @param int $reject
     */
    private function setTotal(array &$total, int $route, int $accept, int $reject): void
    {
        if (isset($total[$route])) {
            $total[$route]['accept'] = $total[$route]['accept'] + $accept;
            $total[$route]['reject'] = $total[$route]['reject'] + $reject;
        } else {
            $total[$route]['accept'] = $accept;
            $total[$route]['reject'] = $reject;
        }
    }

    /**
     * @return array
     */
    public function get(): array
    {
        foreach ($this->getManagers() as $key => $manager) {
            $this->array['managers'][$key]['id'] = $manager->id;
            $this->array['managers'][$key]['name'] = $manager->name;
            foreach ($this->getRoutes() as $route) {
                $acceptOrders = $this->getOrders($route->id, $manager->id);
                $rejectOrders = $this->getOrders($route->id, $manager->id, false);

                $this->setRoute(
                    $key,
                    $route,
                    $this->getDays($acceptOrders, $rejectOrders),
                    $acceptOrders->sum('people_sum'),
                    $rejectOrders->sum('people_sum')
                );
            }
        }

        $this->correctArray();

        foreach ($this->getPeriod() as $date) {
            $this->array['days'][] = $date->format('Y-m-d');
        }

        foreach ($this->hoursPeriods as $hoursPeriod) {
            $this->array['hours'][] = $this->getPeriodName($hoursPeriod);
        }

        return $this->array;
    }
}