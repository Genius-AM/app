<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Day
 *
 * @property int $id
 * @property string $name
 * @property string $weekday
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Busstop[] $busstops
 * @property-read int|null $busstops_count
 * @property-read Collection|Route[] $routes
 * @property-read int|null $routes_count
 * @property-read Collection|Time[] $times
 * @property-read int|null $times_count
 * @method static Builder|Day newModelQuery()
 * @method static Builder|Day newQuery()
 * @method static Builder|Day query()
 * @method static Builder|Day whereCreatedAt($value)
 * @method static Builder|Day whereId($value)
 * @method static Builder|Day whereName($value)
 * @method static Builder|Day whereUpdatedAt($value)
 * @method static Builder|Day whereWeekday($value)
 * @mixin Eloquent
 */
class Day extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'weekday',
    ];

    /**
     * @return BelongsToMany
     */
    public function times(): BelongsToMany
    {
        return $this->belongsToMany(Time::class,'day_time', 'day_id', 'time_id')->withPivot('amount');
    }

    /**
     * @return BelongsToMany
     */
    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'day_route', 'day_id', 'route_id');
    }

    /**
     * @return BelongsToMany
     */
    public function busstops(): BelongsToMany
    {
        return $this->belongsToMany(Busstop::class)->withPivot('time', 'is_evening');
    }
}
