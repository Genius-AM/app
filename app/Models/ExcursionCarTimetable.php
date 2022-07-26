<?php

namespace App\Models;

use App\Cars;
use App\Excursion;
use App\Route;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExcursionCarTimetable
 *
 * @property int $id
 * @property int|null $car_id
 * @property int|null $route_id
 * @property string|null $day
 * @property string|null $time
 * @property string|null $date
 * @property int $self
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Cars|null $car
 * @property-read \App\Route|null $route
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable whereCarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable whereSelf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExcursionCarTimetable whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Excursion[] $excursions
 * @property-read int|null $excursions_count
 */
class ExcursionCarTimetable extends Model
{
    /**
     * @var string
     */
    protected $table = 'excursion_car_timetable';

    /**
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function car()
    {
        return $this->belongsTo(Cars::class, 'car_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function excursions()
    {
        return $this->hasMany(Excursion::class, 'exc_car_timetable_id', 'id');
    }
}
