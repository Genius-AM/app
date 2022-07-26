<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * App\Subcategory
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Collection|Excursion[] $excursions
 * @property-read int|null $excursions_count
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|Route[] $routes
 * @property-read int|null $routes_count
 * @method static Builder|Subcategory newModelQuery()
 * @method static Builder|Subcategory newQuery()
 * @method static Builder|Subcategory query()
 * @method static Builder|Subcategory whereCategoryId($value)
 * @method static Builder|Subcategory whereCreatedAt($value)
 * @method static Builder|Subcategory whereId($value)
 * @method static Builder|Subcategory whereName($value)
 * @method static Builder|Subcategory whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|BookedTime[] $booked_times
 * @property-read int|null $booked_times_count
 */
class Subcategory extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'category_id',
        'name',
	];

    /**
     * @return BelongsTo
     */
	public function category(): BelongsTo
    {
		return $this->belongsTo(Category::class);
	}

    /**
     * @return HasMany
     */
	public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }

    /**
     * @return HasManyThrough
     */
	public function orders(): HasManyThrough
    {
		return $this->hasManyThrough(Order::class, Category::class);
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
    public function booked_times(): HasMany
    {
        return $this->hasMany(BookedTime::class, 'category_id', 'id');
    }
}
