<?php

namespace App\Models;

use App\Category;
use App\Subcategory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    /**
     * @var string
     */
    protected $table = 'promotions';

    /**
     * @var string[]
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}
