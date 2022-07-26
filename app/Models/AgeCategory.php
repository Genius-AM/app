<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgeCategory extends Model
{
    /**
     * App\Address
     *
     * @SWG\Definition (
     *  definition="AgeCategory",
     *  @SWG\Property(
     *      property="id",
     *      type="integer"
     *  ),
     *  @SWG\Property(
     *      property="name",
     *      type="string"
     *  ),
     * )
     */

    /**
     * @var string
     */
    protected $table = 'age_categories';

    protected $appends = ['name'];

    /**
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * @return string
     */
    public function getNameAttribute(): string
    {
        if ($this->from && !$this->to) {
            return $this->from . '+';
        }

        if (!$this->from && $this->to) {
            return 'до ' . $this->to;
        }

        return $this->from . '-' . $this->to;
    }
}
