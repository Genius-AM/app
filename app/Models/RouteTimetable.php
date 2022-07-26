<?php

namespace App\Models;

use App\Route;
use Illuminate\Database\Eloquent\Model;

class RouteTimetable extends Model
{
    /**
     * @var string
     */
    protected $table = 'route_timetable';

    /**
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }
}
