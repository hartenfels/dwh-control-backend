<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Sla\Models\Abstract\SlaDefinitionAbstract;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\EtlMonitor\Sla\Models\DeliverableSlaDefinition
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property float|null $target_percent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EtlMonitor\Sla\Models\DailyTimerange[] $daily_timeranges
 * @property-read int|null $daily_timeranges_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EtlMonitor\Common\Models\History[] $history
 * @property-read int|null $history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EtlMonitor\Common\Models\Property[] $properties
 * @property-read int|null $properties_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EtlMonitor\Sla\Models\DeliverableSla[] $slas
 * @property-read int|null $slas_count
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereTargetPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AvailabilitySlaDefinition extends SlaDefinitionAbstract
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

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'mdi-clipboard-pulse-outline';
    }
}
