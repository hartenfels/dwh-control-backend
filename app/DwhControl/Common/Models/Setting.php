<?php

namespace App\DwhControl\Common\Models;

use App\DwhControl\Common\Traits\HasMultipleDataTypeFieldsTrait;

/**
 * App\DwhControl\Common\Models\Setting
 *
 * @property int $id
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Common\Models\History[] $history
 * @property-read int|null $history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Common\Models\Property[] $properties
 * @property-read int|null $properties_count
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDatatype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValueBigint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValueBoolean($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValueFloat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValueString($value)
 * @mixin \Eloquent
 */
class Setting extends Model
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
     * @param string $name
     * @param null $default
     * @return float|int|bool|string|null
     */
    public static function get(string $name, $default = null)
    {
        return is_null($s = static::where('name', $name)->first()) ? $default : $s->value;
    }

}
