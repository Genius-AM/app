<?php

namespace App;

use App\Models\Company;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property int|null $category_id
 * @property int $role_id
 * @property string|null $device_id
 * @property int|null $balance
 * @property string|null $region
 * @property string|null $address
 * @property string $login
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Cars[] $cars
 * @property-read int|null $cars_count
 * @property-read Collection|UsersDeviceToken[] $deviceToken
 * @property-read int|null $device_token_count
 * @property-read Collection|Excursion[] $excursions
 * @property-read int|null $excursions_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Role $role
 * @property-read Collection|Route[] $routes
 * @property-read int|null $routes_count
 * @method static Builder|User byManager($managerId)
 * @method static Builder|User driver()
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAddress($value)
 * @method static Builder|User whereBalance($value)
 * @method static Builder|User whereCategoryId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeviceId($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLogin($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRegion($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRoleId($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @property int|null $company_id
 * @property-read Company|null $company
 * @property-read Collection|Order[] $driver_orders
 * @property-read int|null $driver_orders_count
 * @property-read Collection|Order[] $refuse_orders
 * @property-read int|null $refuse_orders_count
 * @method static Builder|User manager()
 * @method static Builder|User whereCompanyId($value)
 * @mixin Eloquent
 * @property-read Cars $car
 * @property-read Collection|JwtTokenBlacklist[] $jwt
 * @property-read int|null $jwt_count
 */
class User extends Authenticatable implements JWTSubject
{
	use Notifiable, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var array
     */
    protected $attributes = [
        'balance' => 0
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'role_id',
        'phone',
        'balance',
        'region',
        'address',
        'login',
        'password',
        'category_id',
        'company_id'
    ];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
        'remember_token',
	];

    /**
     * @return HasMany
     */
	public function orders(): HasMany
    {
		return $this->hasMany(Order::class, 'manager_id');
	}

    /**
     * @return HasMany
     */
	public function refuse_orders(): HasMany
    {
		return $this->hasMany(Order::class, 'refuser_id');
	}

    /**
     * @return HasMany
     */
	public function driver_orders(): HasMany
    {
		return $this->hasMany(Order::class, 'driver_id');
	}

    /**
     * @return HasMany
     */
	public function excursions(): HasMany
    {
		return $this->hasMany(Excursion::class, 'driver_id');
	}

    /**
     * @return BelongsTo
     */
	public function role(): BelongsTo
    {
		return $this->belongsTo(Role::class);
	}

    /**
     * @return HasMany
     */
	public function cars(): HasMany
    {
		return $this->hasMany(Cars::class, 'driver_id', 'id');
	}

    /**
     * @return HasOne
     */
	public function car(): HasOne
    {
		return $this->hasOne(Cars::class, 'driver_id', 'id');
	}

    /**
     * @return BelongsToMany
     */
	public function routes(): BelongsToMany
    {
		return $this->belongsToMany(Route::class);
	}

    /**
     * @return HasMany
     */
    public function deviceToken(): HasMany
    {
        return $this->hasMany(UsersDeviceToken::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function jwt(): HasMany
    {
        return $this->hasMany(JwtTokenBlacklist::class, 'user_id', 'id');
    }

    /**
     * @param $query
     * @param $route
     * @return mixed
     */
	public function ScopeByRoute($query, $route)
    {
        return $query->when($route, function (Builder $query, $route) {
            $query->whereHas('routes', function (Builder $q) use ($route) {
                $q->where('route_id', $route);
            });
        });
	}

    /**
     * @param $query
     * @param $id
     * @return mixed
     */
	public function ScopeById($query, $id)
    {
        return $query->when($id, function (Builder $query, $id) {
            $query->where('id', $id);
        });
	}

    /**
     * @param $query
     * @param $role
     * @return mixed
     */
	public function ScopeByRole($query, $role)
    {
        return $query->when($role, function (Builder $query, $role) {
            $query->where('role_id', $role);
        });
	}

    /**
     * @param $query
     * @param $managerId
     * @return mixed
     */
	public function scopeByManager($query, $managerId)
    {
        return $query->when($managerId, function (Builder $query, $managerId) {
            $query->where('id', $managerId);
        });
	}

    /**
     * @param $query
     * @return mixed
     */
	public function scopeDriver($query)
    {
        return $query->where('role_id', Role::DRIVER);
    }

    /**
     * @param $query
     * @return mixed
     */
	public function scopeManager($query)
    {
		return $query->where('role_id', Role::MANAGER);
	}

    /**
     * @return bool
     */
	public function isAdmin(): bool
    {
        return $this->role_id == Role::ADMIN;
    }

    /**
     * @param $role
     * @return bool
     * @throws Exception
     */
    public function isRole($role): bool
    {
        switch ($role) {
            case 'admin':
                $check = Role::ADMIN;
                break;
            case 'dispatcher':
                $check = Role::DISPATCHER;
                break;
            case 'manager':
                $check = Role::MANAGER;
                break;
            case 'driver':
                $check = Role::DRIVER;
                break;
            default:
                throw new Exception("Unknown role");
        }

        if ($this->role_id == $check) {
            return true;
        }

        return false;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * @param $password
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
}
