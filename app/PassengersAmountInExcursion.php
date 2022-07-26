<?php

namespace App;

use App\Models\Company;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\PassengersAmountInExcursion
 *
 * @property int $id
 * @property int $category_id
 * @property int $subcategory_id
 * @property int $route_id
 * @property string $date
 * @property string $time
 * @property int $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PassengersAmountInExcursion newModelQuery()
 * @method static Builder|PassengersAmountInExcursion newQuery()
 * @method static Builder|PassengersAmountInExcursion query()
 * @method static Builder|PassengersAmountInExcursion whereAmount($value)
 * @method static Builder|PassengersAmountInExcursion whereCategoryId($value)
 * @method static Builder|PassengersAmountInExcursion whereCreatedAt($value)
 * @method static Builder|PassengersAmountInExcursion whereDate($value)
 * @method static Builder|PassengersAmountInExcursion whereId($value)
 * @method static Builder|PassengersAmountInExcursion whereRouteId($value)
 * @method static Builder|PassengersAmountInExcursion whereSubcategoryId($value)
 * @method static Builder|PassengersAmountInExcursion whereTime($value)
 * @method static Builder|PassengersAmountInExcursion whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int|null $company_id
 * @property int|null $amount_men
 * @property int|null $amount_women
 * @property int|null $amount_kids
 * @method static Builder|PassengersAmountInExcursion whereAmountKids($value)
 * @method static Builder|PassengersAmountInExcursion whereAmountMen($value)
 * @method static Builder|PassengersAmountInExcursion whereAmountWomen($value)
 * @method static Builder|PassengersAmountInExcursion whereCompanyId($value)
 */
class PassengersAmountInExcursion extends Model
{
    protected $table = 'passengers_amount_in_excursion';

    /**
     * Возврат данных по установленным местам
     *
     * @param $route_id
     * @param $date
     * @param $time
     * @param Company|null $company
     * @return Builder|Model|mixed|object|null
     */
    static public function getDataByParams($route_id, $date, $time, Company $company = null)
    {
        return self::where('route_id', $route_id)
            ->where('date', $date)
            ->where('time', $time)
            ->when($company, function (Builder $query, $company) {
                $query->where('company_id', $company->id);
            })
            ->first();
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        if ($this->amount) {
            return $this->amount;
        }

        return (int)$this->amount_men + (int)$this->amount_women + (int)$this->amount_kids;
    }
}
