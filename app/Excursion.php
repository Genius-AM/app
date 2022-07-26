<?php

namespace App;

use App\Models\ExcursionCarTimetable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Excursion
 *
 * @property int $id
 * @property int $subcategory_id
 * @property int $route_id
 * @property int $car_id
 * @property int $capacity
 * @property int $people
 * @property string $date
 * @property string|null $time
 * @property int $status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Cars $car
 * @property-read User $driver
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|Order[] $ordersParent
 * @property-read int|null $orders_parent_count
 * @property-read Status $status
 * @method static Builder|Excursion byDate($date)
 * @method static Builder|Excursion byRoute($route)
 * @method static Builder|Excursion byStatus($status)
 * @method static Builder|Excursion bySubcategory($subcategory)
 * @method static Builder|Excursion byTime($time)
 * @method static Builder|Excursion newModelQuery()
 * @method static Builder|Excursion newQuery()
 * @method static Builder|Excursion query()
 * @method static Builder|Excursion whereCapacity($value)
 * @method static Builder|Excursion whereCarId($value)
 * @method static Builder|Excursion whereCreatedAt($value)
 * @method static Builder|Excursion whereDate($value)
 * @method static Builder|Excursion whereId($value)
 * @method static Builder|Excursion wherePeople($value)
 * @method static Builder|Excursion whereRouteId($value)
 * @method static Builder|Excursion whereStatusId($value)
 * @method static Builder|Excursion whereSubcategoryId($value)
 * @method static Builder|Excursion whereTime($value)
 * @method static Builder|Excursion whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Route $route
 * @property int|null $exc_car_timetable_id
 * @property-read ExcursionCarTimetable|null $timetable
 * @method static Builder|Excursion active()
 * @method static Builder|Excursion whereExcCarTimetableId($value)
 */
class Excursion extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subcategory_id',
        'route_id',
        'car_id',
        'capacity',
        'people',
        'date',
        'time',
        'status_id',
    ];

    /**
     * @return BelongsTo
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }

    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * @return BelongsToMany
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'excursion_order', 'excursion_id', 'order_id');
    }

    /**
     * @return BelongsToMany
     */
    public function ordersParent(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }

    /**
     * @return BelongsTo
     */
    public function timetable(): BelongsTo
    {
        return $this->belongsTo(ExcursionCarTimetable::class, 'exc_car_timetable_id', 'id');
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

    /**
     * @param $query
     * @param $route
     * @return mixed
     */
    public function scopeByRoute($query, $route)
    {
        return $query->when($route, function (Builder $query, $route) {
            $query->where('route_id', $route);
        });
    }

    /**
     * @param $query
     * @param $status
     * @return mixed
     */
    public function scopeByStatus($query, $status)
    {
        return $query->when($status, function (Builder $query, $status) {
            $query->where('status_id', $status);
        });
    }

    /**
     * @param $query
     * @param $time
     * @return mixed
     */
    public function scopeByTime($query, $time)
    {
        return $query->when($time, function (Builder $query, $time) {
            return $query->where('time', '>=', Carbon::parse($time)->format('H:i'))
                ->when($time == '00:00', function (Builder $query) {
                    $query->where('time', '<', Carbon::parse('12:00')->format('H:i'));
                });
        });
    }

    /**
     * @param $query
     * @param $date
     * @return mixed
     */
    public function scopeByDate($query, $date)
    {
        return $query->when($date, function (Builder $query, $date) {
            $query->where('date', $date);
        });
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status_id', [1, 2, 3])
            ->where('date', '>=', Carbon::now()->format('Y-m-d'));
    }

    /**
     * @return bool
     */
    public function isFormed(): bool
    {
        return $this->status_id == 3;
    }
}
