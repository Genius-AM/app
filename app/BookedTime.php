<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\BookedTime
 *
 * @property int $id
 * @property int $category_id Ид категории
 * @property int $subcategory_id Ид категории
 * @property int $route_id Ид маршрута
 * @property string $date Дата
 * @property string|null $time Время
 * @property int $booked Бронь
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|BookedTime newModelQuery()
 * @method static Builder|BookedTime newQuery()
 * @method static Builder|BookedTime query()
 * @method static Builder|BookedTime whereBooked($value)
 * @method static Builder|BookedTime whereCategoryId($value)
 * @method static Builder|BookedTime whereCreatedAt($value)
 * @method static Builder|BookedTime whereDate($value)
 * @method static Builder|BookedTime whereId($value)
 * @method static Builder|BookedTime whereRouteId($value)
 * @method static Builder|BookedTime whereSubcategoryId($value)
 * @method static Builder|BookedTime whereTime($value)
 * @method static Builder|BookedTime whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Category $category
 * @property-read Route $route
 * @property-read Subcategory $subcategory
 */
class BookedTime extends Model
{
    protected $table = 'booked_time';

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }
}
