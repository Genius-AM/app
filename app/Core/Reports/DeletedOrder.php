<?php

namespace App\Core\Reports;

use App\Order;
use Carbon\Carbon;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DeletedOrder
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
     * @return Order[]|array|BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     */
    private function getOrders()
    {
        return Order::query()
            ->where('status_id', 7)
            ->where('category_id', $this->category)
            ->when($this->subcategories, function (Builder $query, $subcategories) {
                $query->whereIn('subcategory_id', $subcategories);
            })
            ->when($this->companies, function (Builder $query, $companies) {
                return $query->whereIn('company_id', $companies);
            })
            ->when($this->start, function (Builder $query, $start) {
                return $query->where('date', '>=', Carbon::createFromFormat('Y-m-d', $start)->startOfDay());
            })
            ->when($this->end, function (Builder $query, $end) {
                return $query->where('date', '<=', Carbon::createFromFormat('Y-m-d', $end)->endOfDay());
            })
            ->orderBy('date')
            ->orderBy('time')
            ->get();
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = [];

        $orders = $this->getOrders();

        foreach ($orders as $order) {
            $data[] = [
                'category' => $order->category->name,
                'subcategory' => $order->subcategory->name ?? null,
                'company' => $order->company->name ?? null,
                'client' => [
                    'name' => $order->client->name,
                    'phones' => implode(array_filter([
                        $order->client->phone,
                        $order->client->phone_2
                    ]), ', '),
                ],
                'amount' => (int)$order->men + (int)$order->women + (int)$order->kids,
                'date' => Carbon::parse($order->date . ' ' . $order->time)->format('d.m.Y H:i'),
            ];
        }

        return $data;
    }
}