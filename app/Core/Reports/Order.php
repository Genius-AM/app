<?php

namespace App\Core\Reports;

use App\Models\AgeCategory;
use App\Order as OrderModel;
use Carbon\Carbon;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Order
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
     * @return OrderModel[]|array|BuildsQueries[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     */
    private function getOrders()
    {
        return OrderModel::query()
            ->where('category_id', $this->category)
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
     * @return array
     */
    private function getAgeCategories(): array
    {
        return AgeCategory::query()
            ->select('*')
            ->addSelect(DB::raw("0 as amount"))
            ->get()
            ->pluck('amount', 'name')
            ->toArray();
    }

    /**
     * @return array
     */
    private function setAgeCategories($ageCategories, $orderAgeCategories): array
    {
        $array = $ageCategories;

        if ($orderAgeCategories && count($orderAgeCategories)) {
            foreach ($orderAgeCategories as $orderAgeCategory) {
                $array[$orderAgeCategory->name] = $orderAgeCategory->pivot->amount;
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = [];

        $orders = $this->getOrders();

        $ageCategories = $this->getAgeCategories();

        foreach ($orders as $order) {
            $data[] = [
                'category' => $order->category->name,
                'subcategory' => $order->subcategory->name ?? null,
                'company' => $order->company->name ?? null,
                'route' => $order->route->name,
                'client' => [
                    'name' => $order->client->name,
                    'phones' => implode(array_filter([
                        $order->client->phone,
                        $order->client->phone_2
                    ]), ', '),
                    'comment' => $order->client->comment,
                ],
                'amount' => (int)$order->men + (int)$order->women + (int)$order->kids,
                'men' => $order->men,
                'women' => $order->women,
                'kids' => $order->kids,
                'date' => Carbon::parse($order->date . ' ' . $order->time)->format('d.m.Y H:i'),
                'manager' => $order->manager ? $order->manager->name : null,
                'address' => $order->address,
                'price' => $order->price,
                'prepayment' => $order->prepayment,
                'status' => $order->status->name,
                'refuser' => $order->refuser ? $order->refuser->name : null,
                'reason' => $order->reason,
                'driver' => [
                    'name' => $order->driver ? $order->driver->name : null,
                    'phone' => $order->driver ? $order->driver->phone : null,
                ],
                'created_at' => $order->created_at->format('d.m.Y H:i'),
                'ageCategories' => $this->setAgeCategories($ageCategories, $order->age_categories),
            ];
        }

        return $data;
    }
}