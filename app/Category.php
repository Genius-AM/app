<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * App\Category
 *
 * @property int $id
 * @property string $name
 * @property int $default_seats_on_vehicle
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Cars[] $cars
 * @property-read int|null $cars_count
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|Route[] $routes
 * @property-read int|null $routes_count
 * @property-read Collection|Subcategory[] $subcategories
 * @property-read int|null $subcategories_count
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDefaultSeatsOnVehicle($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @mixin Eloquent
 */

/**
 * App\Category
 *
 * @SWG\Definition (
 *  definition="Category",
 *  @SWG\Property(
 *      property="id",
 *      type="integer"
 *  ),
 *  @SWG\Property(
 *      property="name",
 *      type="string"
 *  ),
 * )
 * @property int $id
 * @property string $name
 * @property int $default_seats_on_vehicle
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Cars[] $cars
 * @property-read int|null $cars_count
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|Route[] $routes
 * @property-read int|null $routes_count
 * @property-read Collection|Subcategory[] $subcategories
 * @property-read int|null $subcategories_count
 * @method static Builder|Category activeCategories()
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDefaultSeatsOnVehicle($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|BookedTime[] $booked_times
 * @property-read int|null $booked_times_count
 */

class Category extends Model
{
    const DJIPPING = 1;
    const DIVING = 2;
    const QUADBIKE = 3;
    const SEA = 4;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
	];

    /**
     * @return HasMany
     */
	public function subcategories(): HasMany
    {
		return $this->hasMany(Subcategory::class);
	}

    /**
     * @return HasManyThrough
     */
    public function routes(): HasManyThrough
    {
        return $this->hasManyThrough(Route::class, Subcategory::class);
    }

    /**
     * @return HasMany
     */
	public function orders(): HasMany
    {
		return $this->hasMany(Order::class);
	}

    /**
     * @return HasMany
     */
	public function cars(): HasMany
    {
		return $this->hasMany(Cars::class);
	}

    /**
     * @return HasMany
     */
	public function booked_times(): HasMany
    {
        return $this->hasMany(BookedTime::class, 'category_id', 'id');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActiveCategories($query)
    {
        return $query->whereIn('id', [1,2,3,4]);
    }
}
