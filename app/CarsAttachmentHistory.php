<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\CarsAttachmentHistory
 *
 * @property int $id
 * @property int|null $sort Номер сортировка
 * @property int $car_id Id машины
 * @property int|null $driver_id Id водителя
 * @property string|null $end_attach Конец закрепления
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Cars $car
 * @property-read User|null $driver
 * @method static Builder|CarsAttachmentHistory history($id)
 * @method static Builder|CarsAttachmentHistory newModelQuery()
 * @method static Builder|CarsAttachmentHistory newQuery()
 * @method static Builder|CarsAttachmentHistory query()
 * @method static Builder|CarsAttachmentHistory whereBeginAttach($value)
 * @method static Builder|CarsAttachmentHistory whereCarId($value)
 * @method static Builder|CarsAttachmentHistory whereCreatedAt($value)
 * @method static Builder|CarsAttachmentHistory whereDriverId($value)
 * @method static Builder|CarsAttachmentHistory whereEndAttach($value)
 * @method static Builder|CarsAttachmentHistory whereId($value)
 * @method static Builder|CarsAttachmentHistory whereSort($value)
 * @method static Builder|CarsAttachmentHistory whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CarsAttachmentHistory extends Model
{
    /**
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeHistory($query, $id)
    {
        return $query->where('car_id', $id)->orderBy('id', 'desc');
    }

    /**
     * Связи к водителям
     *
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Связи к машинам
     *
     * @return BelongsTo
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Cars::class);
    }
}
