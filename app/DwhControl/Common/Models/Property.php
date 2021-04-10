<?php

namespace App\DwhControl\Common\Models;

use App\DwhControl\Common\Traits\HasMultipleDataTypeFieldsTrait;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\DwhControl\Common\Models\Property
 *
 * @property int $id
 * @property int $belongsToModel_id
 * @property string $belongsToModel_type
 * @property string $type
 * @property string $name
 * @property string $datatype
 * @property string|null $value_string
 * @property int|null $value_bigint
 * @property float|null $value_float
 * @property int|null $value_boolean
 * @property array|null $value_json_array
 * @property object|null $value_json_object
 * @property bool|null $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $belongsToModel
 * @property-read string $full_name
 * @property-read mixed $value
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Common\Models\History[] $history
 * @property-read int|null $history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Property[] $properties
 * @property-read int|null $properties_count
 * @method static \Illuminate\Database\Eloquent\Builder|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property query()
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereBelongsToModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereBelongsToModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereDatatype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereValueBigint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereValueBoolean($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereValueFloat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereValueJsonArray($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereValueJsonObject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereValueString($value)
 * @mixin \Eloquent
 */
class Property extends Model
{

    use HasMultipleDataTypeFieldsTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'datatype', 'is_active',
        'value_string', 'value_bigint', 'value_float', 'value_boolean', 'value_json_array', 'value_json_object',
        'belongsToModel_type', 'belongsToModel_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'value_json_array' => 'array',
        'value_json_object' => 'object'
    ];

    /**
     * @return MorphTo
     */
    public function belongsToModel(): MorphTo
    {
        return $this->morphTo('belongsToModel');
    }

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return sprintf('%s.%s', $this->belongsToModel_type, $this->name);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $field = 'value_' . $this->datatype;
        return $this->$field;
    }

}
