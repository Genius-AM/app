<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Pack
 *
 * @property int $id
 * @property string $name
 * @property int $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Route[] $includes
 * @property-read int|null $includes_count
 * @method static Builder|Pack newModelQuery()
 * @method static Builder|Pack newQuery()
 * @method static Builder|Pack query()
 * @method static Builder|Pack whereCreatedAt($value)
 * @method static Builder|Pack whereId($value)
 * @method static Builder|Pack whereName($value)
 * @method static Builder|Pack wherePrice($value)
 * @method static Builder|Pack whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Pack extends Model
{
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
        'price',
	];

    /**
     * @return BelongsToMany
     */
	public function includes(): BelongsToMany
    {
		return $this->belongsToMany(Route::class);
	}
}
