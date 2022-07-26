<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Status
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Excursion[] $excursions
 * @property-read int|null $excursions_count
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @method static Builder|Status newModelQuery()
 * @method static Builder|Status newQuery()
 * @method static Builder|Status query()
 * @method static Builder|Status whereCreatedAt($value)
 * @method static Builder|Status whereId($value)
 * @method static Builder|Status whereName($value)
 * @method static Builder|Status whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Status extends Model
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
     * @return HasMany
     */
	public function orders(): HasMany
    {
		return $this->hasMany(Order::class);
	}

    /**
     * @return HasMany
     */
    public function excursions(): HasMany
    {
        return $this->hasMany(Excursion::class);
    }
}
