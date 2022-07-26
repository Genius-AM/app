<?php

namespace App;

use App\Models\ExcursionCarTimetable;
use App\Models\RouteCar;
use App\Models\RouteTimetable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Route
 *
 * @property int $id
 * @property int $subcategory_id
 * @property string $name
 * @property int $price_men
 * @property int $price_women
 * @property int $price_kids
 * @property int $prepayment
 * @property int $is_payable
 * @property string|null $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|BookedTime[] $booked
 * @property-read int|null $booked_count
 * @property-read Collection|Day[] $days
 * @property-read int|null $days_count
 * @property-read Collection|Pack[] $packs
 * @property-read int|null $packs_count
 * @property-read Subcategory $subcategory
 * @property-read Collection|Time[] $times
 * @property-read int|null $times_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Route bySubcategory($subcategory)
 * @method static Builder|Route newModelQuery()
 * @method static Builder|Route newQuery()
 * @method static Builder|Route query()
 * @method static Builder|Route whereColor($value)
 * @method static Builder|Route whereCreatedAt($value)
 * @method static Builder|Route whereId($value)
 * @method static Builder|Route whereIsPayable($value)
 * @method static Builder|Route whereName($value)
 * @method static Builder|Route wherePrepayment($value)
 * @method static Builder|Route wherePriceKids($value)
 * @method static Builder|Route wherePriceMen($value)
 * @method static Builder|Route wherePriceWomen($value)
 * @method static Builder|Route whereSubcategoryId($value)
 * @method static Builder|Route whereUpdatedAt($value)
 * @mixin Eloquent
 */

/**
 * App\Route
 *
 * @SWG\Definition (
 *  definition="Route",
 *  @SWG\Property(
 *      property="id",
 *      type="integer"
 *  ),
 *  @SWG\Property(
 *      property="name",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="price_men",
 *      type="integer"
 *  ),
 *  @SWG\Property(
 *      property="price_women",
 *      type="integer"
 *  ),
 *  @SWG\Property(
 *      property="price_kids",
 *      type="integer"
 *  ),
 *  @SWG\Property(
 *      property="price",
 *      type="integer"
 *  ),
 * )
 * @property int $id
 * @property int|null $category_id
 * @property int|null $subcategory_id
 * @property string $name
 * @property int|null $price_men
 * @property int|null $price_women
 * @property int|null $price_kids
 * @property int|null $price
 * @property int $prepayment
 * @property int $is_payable
 * @property string|null $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|BookedTime[] $booked
 * @property-read int|null $booked_count
 * @property-read Collection|Day[] $days
 * @property-read int|null $days_count
 * @property-read Collection|Excursion[] $excursions
 * @property-read int|null $excursions_count
 * @property-read Collection|Pack[] $packs
 * @property-read int|null $packs_count
 * @property-read Subcategory|null $subcategory
 * @property-read Collection|Time[] $times
 * @property-read int|null $times_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Route bySubcategory($subcategory)
 * @method static Builder|Route newModelQuery()
 * @method static Builder|Route newQuery()
 * @method static Builder|Route query()
 * @method static Builder|Route whereCategoryId($value)
 * @method static Builder|Route whereColor($value)
 * @method static Builder|Route whereCreatedAt($value)
 * @method static Builder|Route whereId($value)
 * @method static Builder|Route whereIsPayable($value)
 * @method static Builder|Route whereName($value)
 * @method static Builder|Route wherePrepayment($value)
 * @method static Builder|Route wherePrice($value)
 * @method static Builder|Route wherePriceKids($value)
 * @method static Builder|Route wherePriceMen($value)
 * @method static Builder|Route wherePriceWomen($value)
 * @method static Builder|Route whereSubcategoryId($value)
 * @method static Builder|Route whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|Cars[] $cars
 * @property-read int|null $cars_count
 * @property-read Collection|RouteCar[] $route_car
 * @property-read int|null $route_car_count
 * @property-read Collection|ExcursionCarTimetable[] $timetables
 * @property-read int|null $timetables_count
 * @property string|null $duration
 * @method static Builder|Route whereDuration($value)
 */

class Route extends Model
{
    const colors = [
        'blue' => 'Синий',
        'pink' => 'Розовый',
        'green' => 'Зеленый',
        'grey' => 'Серый',
        'yellow' => 'Желтый',
        'orange' => 'Оранжевый',
        'red' => 'Красный',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subcategory_id',
        'name',
        'price_men',
        'price_women',
        'price_kids',
        'prepayment',
        'is_payable',
    ];

    /**
     * @return BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * @return BelongsToMany
     */
    public function packs(): BelongsToMany
    {
        return $this->belongsToMany(Pack::class);
    }

    /**
     * @return BelongsToMany
     */
    public function times(): BelongsToMany
    {
        return $this->belongsToMany(Time::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTrashed();
    }

    /**
     * @return HasMany
     */
    public function excursions(): HasMany
    {
        return $this->hasMany(Excursion::class);
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return BelongsToMany
     */
    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class, 'day_route', 'route_id', 'day_id')->withPivot('car_id');
    }

    /**
     * @return HasMany
     */
    public function booked(): HasMany
    {
        return $this->hasMany(BookedTime::class, 'route_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function route_car(): HasMany
    {
        return $this->hasMany(RouteCar::class, 'route_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Cars::class, 'route_car', 'route_id', 'car_id');
    }

    /**
     * @return HasMany
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(ExcursionCarTimetable::class, 'route_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function route_timetables(): HasMany
    {
        return $this->hasMany(RouteTimetable::class, 'route_id', 'id');
    }

    /**
     * @param $query
     * @param $subcategory
     * @return mixed
     */
    public function scopeBySubcategory($query, $subcategory)
    {
        return $query->when($subcategory, function (Builder $query, $subcategory) {
            $query->where('subcategory_id', $subcategory);
        });
    }
}
