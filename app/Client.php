<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Client
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $phone_2
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @method static Builder|Client newModelQuery()
 * @method static Builder|Client newQuery()
 * @method static Builder|Client query()
 * @method static Builder|Client whereComment($value)
 * @method static Builder|Client whereCreatedAt($value)
 * @method static Builder|Client whereId($value)
 * @method static Builder|Client whereName($value)
 * @method static Builder|Client wherePhone($value)
 * @method static Builder|Client wherePhone2($value)
 * @method static Builder|Client whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Client extends Model
{
    protected $table = 'clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'phone_2',
        'comment',
    ];

    /**
     * @return HasMany
     */
	public function orders(): HasMany
    {
		return $this->hasMany(Order::class);
	}
}
