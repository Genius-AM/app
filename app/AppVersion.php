<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\AppVersion
 *
 * @property int $id
 * @property string $version
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|AppVersion newModelQuery()
 * @method static Builder|AppVersion newQuery()
 * @method static Builder|AppVersion query()
 * @method static Builder|AppVersion whereCreatedAt($value)
 * @method static Builder|AppVersion whereId($value)
 * @method static Builder|AppVersion whereUpdatedAt($value)
 * @method static Builder|AppVersion whereVersion($value)
 * @mixin Eloquent
 */
class AppVersion extends Model
{
    protected $table = 'app_version';
}
