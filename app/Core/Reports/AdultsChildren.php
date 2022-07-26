<?php

namespace App\Core\Reports;

use App\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdultsChildren
{
    protected $category;
    protected $subcategories = [];
    protected $companies = [];
    protected $start = null;
    protected $end = null;

    /**
     * AgeCategory constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->category = $params['category'];
        $this->subcategories = array_key_exists('subcategories', $params) ? array_filter($params['subcategories']) : [];
        $this->companies = array_key_exists('companies', $params) ? $params['companies'] : [];
        $this->start = array_key_exists('start', $params) ? $params['start'] : null;
        $this->end = array_key_exists('end', $params) ? $params['end'] : null;
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
     * @param User $manager
     * @return Order[]|array|BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     */
    private function getOrders(User $manager)
    {
        return Order::query()
            ->where('category_id', $this->category)
            ->where('manager_id', $manager->id)
            ->when($this->subcategories, function (Builder $query, $subcategories) {
                $query->whereIn('subcategory_id', $subcategories);
            })
            ->when($this->companies, function (Builder $query, $companies) {
                return $query->whereIn('company_id', $companies);
            })
            ->when($this->start, function (Builder $query, $start) {
                return $query->where(DB::raw('CONCAT(date, \' \', time)'), '>=', Carbon::createFromFormat("Y-m-d\TH:i", $start));
            })
            ->when($this->end, function (Builder $query, $end) {
                return $query->where(DB::raw('CONCAT(date, \' \', time)'), '<=', Carbon::createFromFormat("Y-m-d\TH:i", $end));
            })
            ->orderBy('date')
            ->orderBy('time')
            ->get();
    }

    /**
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getManagers()
    {
        return User::where('role_id', 1)
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = [];

        $managers = $this->getManagers();

        foreach ($managers as $manager) {
            $item = [
                'manager' => $manager->name,
                'adults' => 0,
                'children' => 0,
            ];

            $orders = $this->getOrders($manager);

            foreach ($orders as $order) {
                $item['adults'] += $order->men + $order->women;
                $item['children'] += $order->kids;
            }

            $data[] = $item;
        }

        return $data;
    }
}