<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Time
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Day[] $days
 * @property-read int|null $days_count
 * @property-read Collection|Route[] $routes
 * @property-read int|null $routes_count
 * @method static Builder|Time newModelQuery()
 * @method static Builder|Time newQuery()
 * @method static Builder|Time query()
 * @method static Builder|Time whereCreatedAt($value)
 * @method static Builder|Time whereId($value)
 * @method static Builder|Time whereName($value)
 * @method static Builder|Time whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Time extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return BelongsToMany
     */
    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class);
    }

    /**
     * @return BelongsToMany
     */
    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class,'day_time', 'time_id', 'day_id');
    }
}
