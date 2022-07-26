<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\UsersDeviceToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $device_id
 * @property string $token
 * @property int $device_android
 * @property int $device_ios
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $users
 * @method static Builder|UsersDeviceToken newModelQuery()
 * @method static Builder|UsersDeviceToken newQuery()
 * @method static Builder|UsersDeviceToken query()
 * @method static Builder|UsersDeviceToken whereCreatedAt($value)
 * @method static Builder|UsersDeviceToken whereDeviceAndroid($value)
 * @method static Builder|UsersDeviceToken whereDeviceId($value)
 * @method static Builder|UsersDeviceToken whereDeviceIos($value)
 * @method static Builder|UsersDeviceToken whereId($value)
 * @method static Builder|UsersDeviceToken whereToken($value)
 * @method static Builder|UsersDeviceToken whereUpdatedAt($value)
 * @method static Builder|UsersDeviceToken whereUserId($value)
 * @mixin Eloquent
 */
class UsersDeviceToken extends Model
{
    protected $table = 'users_device_token';

    /**
     * @return BelongsTo
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
}
