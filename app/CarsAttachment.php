<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\CarsAttachment
 *
 * @method static Builder|CarsAttachment attach($id)
 * @method static Builder|CarsAttachment newModelQuery()
 * @method static Builder|CarsAttachment newQuery()
 * @method static Builder|CarsAttachment query()
 * @mixin Eloquent
 */
class CarsAttachment extends Model
{
    /**
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeAttach($query, $id) {
        return $query->where('car_id', $id);
    }
}
