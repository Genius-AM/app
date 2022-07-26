<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Address
 *
 * @SWG\Definition (
 *  definition="Address",
 *  @SWG\Property(
 *      property="id",
 *      type="integer"
 *  ),
 *  @SWG\Property(
 *      property="name",
 *      type="string"
 *  ),
 * )
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @method static Builder|Address newModelQuery()
 * @method static Builder|Address newQuery()
 * @method static Builder|Address query()
 * @method static Builder|Address whereCreatedAt($value)
 * @method static Builder|Address whereId($value)
 * @method static Builder|Address whereName($value)
 * @method static Builder|Address whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Address extends Model
{
    protected $table = 'addresses';

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'point_id', 'id');
    }
}
