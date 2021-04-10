<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Sla\Models\Abstract\SlaProgressAbstract;

/**
 * App\DwhControl\Sla\Models\DeliverableSlaProgress
 *
 * @property int $id
 * @property int $sla_id
 * @property string $type
 * @property string $time
 * @property float $progress_percent
 * @property string $source
 * @property int|null $is_override
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Common\Models\History[] $history
 * @property-read int|null $history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Common\Models\Property[] $properties
 * @property-read int|null $properties_count
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress whereIsOverride($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress whereProgressPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress whereSlaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaProgress whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AvailabilitySlaProgress extends SlaProgressAbstract
{

    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'availability'
    ];

    /**
     * @var string
     */
    protected static string $type = 'availability';

}
