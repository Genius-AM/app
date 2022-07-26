<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\ExcursionOrder
 *
 * @property int $id
 * @property int $excursion_id
 * @property int $order_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ExcursionOrder newModelQuery()
 * @method static Builder|ExcursionOrder newQuery()
 * @method static Builder|ExcursionOrder query()
 * @method static Builder|ExcursionOrder whereCreatedAt($value)
 * @method static Builder|ExcursionOrder whereExcursionId($value)
 * @method static Builder|ExcursionOrder whereId($value)
 * @method static Builder|ExcursionOrder whereOrderId($value)
 * @method static Builder|ExcursionOrder whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ExcursionOrder extends Model
{
    public $table = 'excursion_order';
}
