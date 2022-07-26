<?php

namespace App;

use App\Models\AgeCategory;
use App\Models\Company;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

/**
 * App\Order
 *
 * @property int $id
 * @property int $category_id
 * @property int $subcategory_id
 * @property int $route_id
 * @property int $client_id
 * @property string $date
 * @property string|null $time
 * @property int $manager_id
 * @property int $men
 * @property int $women
 * @property int $kids
 * @property int $price
 * @property int $prepayment
 * @property int $status_id
 * @property string|null $reason
 * @property int|null $food
 * @property int $is_pack
 * @property int|null $pack_id
 * @property int $pack_created
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Client $client
 * @property-read Collection|Excursion[] $excursion
 * @property-read int|null $excursion_count
 * @property-read User $manager
 * @property-read Pack|null $pack
 * @property-read Status $status
 * @property-read Subcategory $subcategory
 * @method static Builder|Order byCategory($category)
 * @method static Builder|Order byDate($date)
 * @method static Builder|Order byDates($request)
 * @method static Builder|Order byManager($manager)
 * @method static Builder|Order byRoute($route)
 * @method static Builder|Order byStatus($status)
 * @method static Builder|Order bySubcategory($subcategory)
 * @method static Builder|Order byTime($time)
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereAddress($value)
 * @method static Builder|Order whereCategoryId($value)
 * @method static Builder|Order whereClientId($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereDate($value)
 * @method static Builder|Order whereFood($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereIsPack($value)
 * @method static Builder|Order whereKids($value)
 * @method static Builder|Order whereManagerId($value)
 * @method static Builder|Order whereMen($value)
 * @method static Builder|Order wherePackCreated($value)
 * @method static Builder|Order wherePackId($value)
 * @method static Builder|Order wherePrepayment($value)
 * @method static Builder|Order wherePrice($value)
 * @method static Builder|Order whereReason($value)
 * @method static Builder|Order whereRouteId($value)
 * @method static Builder|Order whereStatusId($value)
 * @method static Builder|Order whereSubcategoryId($value)
 * @method static Builder|Order whereTime($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereWomen($value)
 * @mixin Eloquent
 * @property \Illuminate\Support\Carbon|null $last_call
 * @property int $calls_count
 * @property Address|null $address
 * @property int|null $address_id
 * @property int|null $refuser_id
 * @property int|null $driver_id
 * @property int|null $company_id
 * @property int|null $rent
 * @property-read Company|null $company
 * @property-read User|null $driver
 * @property-read User|null $refuser
 * @property-read Route|null $route
 * @method static Builder|Order whereAddressId($value)
 * @method static Builder|Order whereCallsCount($value)
 * @method static Builder|Order whereCompanyId($value)
 * @method static Builder|Order whereDriverId($value)
 * @method static Builder|Order whereLastCall($value)
 * @method static Builder|Order whereRefuserId($value)
 * @method static Builder|Order whereRent($value)
 * @property int|null $point_id
 * @property-read Address|null $point
 * @method static Builder|Order wherePointId($value)
 */
class Order extends Model
{
    /**
     * @var string
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'route_id',
        'client_id',
        'time',
        'date',
        'manager_id',
        'men',
        'women',
        'kids',
        'price',
        'address',
        'point_id',
        'prepayment',
        'status_id',
        'is_pack',
        'pack_id',
        'pack_created',
    ];

    protected $dates = [
        'last_call',
        'created_at'
    ];

    /**
     * @return BelongsTo
     */
    public function point(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'point_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * @return BelongsTo
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * @return BelongsTo
     */
    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsToMany
     */
    public function excursion(): BelongsToMany
    {
        return $this->belongsToMany(Excursion::class, 'excursion_order', 'order_id', 'excursion_id');
    }

    /**
     * @return BelongsTo
     */
    public function refuser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refuser_id')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id')->withTrashed();
    }

    /**
     * @return BelongsToMany
     */
    public function age_categories(): BelongsToMany
    {
        return $this->belongsToMany(AgeCategory::class, 'age_category_order')->withPivot('amount');
    }

    /**
     * @param $query
     * @param $category
     * @return mixed
     */
    public function scopeByCategory($query, $category)
    {
        return $query->when($category, function (Builder $query, $category) {
            $query->where('category_id', $category);
        });
    }

    /**
     * @param $query
     * @param $subcategory
     * @return mixed
     */
    public function scopeBySubcategory($query, $subcategory)
    {
        return $query->when($subcategory, function (Builder $query, $subcategory) {
            $query->where('subcategory_id', $subcategory);
        });
    }

    /**
     * @param $query
     * @param $route
     * @return mixed
     */
    public function scopeByRoute($query, $route)
    {
        return $query->when($route, function (Builder $query, $route) {
            $query->where('route_id', $route);
        });
    }

    /**
     * @param $query
     * @param $manager
     * @return mixed
     */
    public function scopeByManager($query, $manager)
    {
        return $query->when($manager, function (Builder $query, $manager) {
            $query->where('manager_id', $manager);
        });
    }

    /**
     * @param $query
     * @param $time
     * @return mixed
     */
    public function scopeByTime($query, $time)
    {
        return $query->when($time, function (Builder $query, $time) {
            return $query->where('time', '>=', Carbon::parse($time)->format('H:i'))
                ->when($time == '00:00', function (Builder $query) {
                    $query->where('time', '<', Carbon::parse('12:00')->format('H:i'));
                });
        });
    }

    /**
     * @param $query
     * @param $date
     * @return mixed
     */
    public function scopeByDate($query, $date)
    {
        return $query->when($date, function (Builder $query, $date) {
            $query->where('date', $date);
        });
    }

    /**
     * @param $query
     * @param $status
     * @return mixed
     */
    public function scopeByStatus($query, $status)
    {
        return $query->when($status, function (Builder $query, $status) {
            $query->where('status_id', $status);
        });
    }

    /**
     * @return int
     */
    public function peopleSum(): int
    {
        return $this->men + $this->women + $this->kids;
    }

    /**
     * @return int
     */
    public function getPeopleSumAttribute(): int
    {
        return $this->peopleSum();
    }

    /**
     * @param $query
     * @param Request $request
     * @return mixed
     */
    public function scopeByDates($query, Request $request)
    {
        return $query
            ->when($request->input('start'), function ($query, $start) {
                return $query->where('created_at', '>=', Carbon::createFromFormat('Y-m-d', $start)->startOfDay());
            })
            ->when($request->input('end'), function ($query, $end) {
                return $query->where('created_at', '<=', Carbon::createFromFormat('Y-m-d', $end)->endOfDay());
            });
    }
}
