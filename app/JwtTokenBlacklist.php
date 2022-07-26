<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\JwtTokenBlacklist
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|JwtTokenBlacklist newModelQuery()
 * @method static Builder|JwtTokenBlacklist newQuery()
 * @method static Builder|JwtTokenBlacklist query()
 * @method static Builder|JwtTokenBlacklist whereCreatedAt($value)
 * @method static Builder|JwtTokenBlacklist whereId($value)
 * @method static Builder|JwtTokenBlacklist whereToken($value)
 * @method static Builder|JwtTokenBlacklist whereUpdatedAt($value)
 * @method static Builder|JwtTokenBlacklist whereUserId($value)
 * @mixin Eloquent
 * @property-read User $user
 */
class JwtTokenBlacklist extends Model
{
    protected $table = 'jwt_token_blacklist';

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
}
