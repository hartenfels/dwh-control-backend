<?php

namespace App\EtlMonitor\Common\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

/**
 * App\EtlMonitor\Common\Models\History
 *
 * @property int $id
 * @property int $belongsToModel_id
 * @property string $belongsToModel_type
 * @property string $event
 * @property string $params
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $belongsToModel
 * @property-read \Illuminate\Database\Eloquent\Collection|History[] $history
 * @property-read int|null $history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EtlMonitor\Common\Models\Property[] $properties
 * @property-read int|null $properties_count
 * @method static \Illuminate\Database\Eloquent\Builder|History newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|History newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|History query()
 * @method static \Illuminate\Database\Eloquent\Builder|History whereBelongsToModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereBelongsToModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|History whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class History extends Model
{

    /**
     * @var array
     */
    public ?array $transformable = [
        'event', 'params'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'event', 'params', 'created_at'
    ];

    /**
     * @return MorphTo
     */
    public function belongsToModel(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return Collection
     */
    public function transform(): Collection
    {
        $params = explode('||', $this->params);
        $params = array_map(function ($param) {
            if (is_a($param, Model::class)) {
                $obj = explode('::', $param);
                $model = $obj[0]::find($obj[1]);
                if (is_null($model)) {
                    return $param;
                }

                return $model->transform();
            }

            return $param;
        }, $params);

        return collect([
            'id' => $this->id,
            'event' => $this->event,
            'params' => $params
        ]);
    }

}
