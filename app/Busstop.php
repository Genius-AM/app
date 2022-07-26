<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Busstop
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Day[] $days
 * @property-read int|null $days_count
 * @method static Builder|Busstop newModelQuery()
 * @method static Builder|Busstop newQuery()
 * @method static Builder|Busstop query()
 * @method static Builder|Busstop whereCreatedAt($value)
 * @method static Builder|Busstop whereId($value)
 * @method static Builder|Busstop whereName($value)
 * @method static Builder|Busstop whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Busstop extends Model
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
    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class)->withPivot('time', 'is_evening');
    }
}
