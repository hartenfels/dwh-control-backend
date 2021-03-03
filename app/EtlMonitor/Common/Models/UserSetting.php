<?php

namespace App\EtlMonitor\Common\Models;

use App\EtlMonitor\Common\Traits\HasMultipleDataTypeFieldsTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\EtlMonitor\Common\Models\UserSetting
 *
 * @property int $user_id
 * @property string $name
 * @property string $datatype
 * @property string|null $value_string
 * @property int|null $value_bigint
 * @property float|null $value_float
 * @property int|null $value_boolean
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $value
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EtlMonitor\Common\Models\History[] $history
 * @property-read int|null $history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EtlMonitor\Common\Models\Property[] $properties
 * @property-read int|null $properties_count
 * @property-read \App\EtlMonitor\Common\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereDatatype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereValueBigint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereValueBoolean($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereValueFloat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereValueString($value)
 * @mixin \Eloquent
 */
class UserSetting extends Model
{

    use HasMultipleDataTypeFieldsTrait;

    /**
     * @var array
     */
    public ?array $transformable = [
        'name', 'value', 'is_active'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param string $name
     * @param null $default
     * @return float|int|bool|string|null
     */
    public static function get(string $name, $default = null)
    {
        return is_null($s = static::where('name', $name)->first()) ? $default : $s->value;
    }

}
