<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class TrackingOperation extends Model
{
    /**
     * @var string
     */
    protected $table = 'tracking_operations';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
