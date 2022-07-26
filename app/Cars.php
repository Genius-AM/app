<?php

namespace App;

use App\Models\ExcursionCarTimetable;
use App\Models\RouteCar;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Cars
 *
 * @property int $id
 * @property string $name Название машины
 * @property string $car_number Гос номер
 * @property int $category_id
 * @property int $passengers_amount Количество пассажиров
 * @property string $owner Чья машина (наша или партнеров)
 * @property string|null $owner_name Если машина партнеров, то здесь будет имя партнера
 * @property int|null $driver_id Id водителя
 * @property int $order Номер сортировки
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category $category
 * @property-read User|null $driver
 * @method static Builder|Cars newModelQuery()
 * @method static Builder|Cars newQuery()
 * @method static Builder|Cars query()
 * @method static Builder|Cars whereCarNumber($value)
 * @method static Builder|Cars whereCategoryId($value)
 * @method static Builder|Cars whereCreatedAt($value)
 * @method static Builder|Cars whereDriverId($value)
 * @method static Builder|Cars whereId($value)
 * @method static Builder|Cars whereName($value)
 * @method static Builder|Cars whereOrder($value)
 * @method static Builder|Cars whereOwner($value)
 * @method static Builder|Cars whereOwnerName($value)
 * @method static Builder|Cars wherePassengersAmount($value)
 * @method static Builder|Cars whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Collection|Excursion[] $excursions
 * @property-read int|null $excursions_count
 * @property-read Collection|RouteCar[] $route_car
 * @property-read int|null $route_car_count
 * @property-read Collection|Route[] $routes
 * @property-read int|null $routes_count
 * @property-read Collection|ExcursionCarTimetable[] $timetables
 * @property-read int|null $timetables_count
 */
class Cars extends Model
{
    /**
     * Связи к водителям
     *
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    /**
     * Связи к категории
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany
     */
    public function excursions(): HasMany
    {
        return $this->hasMany(Excursion::class, 'car_id');
    }

    /**
     * @return HasMany
     */
    public function route_car(): HasMany
    {
        return $this->hasMany(RouteCar::class, 'car_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_car', 'car_id', 'route_id');
    }

    /**
     * @return HasMany
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(ExcursionCarTimetable::class, 'car_id', 'id');
    }
}
