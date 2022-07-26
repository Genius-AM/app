<?php

namespace App\Models;

use App\Cars;
use App\Order;
use App\Route;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RouteCar
 *
 * @property int $id
 * @property int|null $car_id
 * @property int|null $route_id
 * @property int|null $price_men
 * @property int|null $price_women
 * @property int|null $price_kids
 * @property int|null $price
 * @property int $prepayment
 * @property int $is_payable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Cars|null $car
 * @property-read \App\Route|null $route
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar whereCarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar whereIsPayable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar wherePrepayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar wherePriceKids($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar wherePriceMen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar wherePriceWomen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $duration
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RouteCar whereDuration($value)
 */
class RouteCar extends Model
{
    /**
     * @var string
     */
    protected $table = 'route_car';

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
}
