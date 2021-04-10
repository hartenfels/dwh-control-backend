<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Sla\Models\Abstract\TimerangeAbstract;
use App\DwhControl\Sla\Models\Interfaces\SlaInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * App\DwhControl\Sla\Models\DailyTimerange
 *
 * @property int $id
 * @property int $sla_definition_id
 * @property int $anchor
 * @property string $range_start
 * @property string $range_end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Common\Models\History[] $history
 * @property-read int|null $history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Common\Models\Property[] $properties
 * @property-read int|null $properties_count
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange query()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange whereAnchor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange whereRangeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange whereRangeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange whereSlaDefinitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyTimerange whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WeeklyTimerange extends TimerangeAbstract
{

    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'weekly'
    ];

    /**
     * @var string
     */
    protected static string $type = 'weekly';

    /**
     * @param CarbonInterface|null $time
     * @return CarbonInterface
     */
    public function start(CarbonInterface $time = null): CarbonInterface
    {
        $t = ($time ? clone $time : Carbon::now())->startOfWeek();
        list($hours, $minutes) = explode(':', $this->range_start);
        $t->addHours($hours)->addMInutes($minutes); // Set time

        return $t;
    }

    /**
     * @param CarbonInterface|null $time
     * @return CarbonInterface
     */
    public function end(CarbonInterface $time = null): CarbonInterface
    {
        $t = ($time ? clone $time : Carbon::now())->startOfWeek();
        list($hours, $minutes) = explode(':', $this->range_end);
        $t->addHours($hours)->addMInutes($minutes); // Set time

        return $t;
    }

    /**
     * @param CarbonInterface|null $time
     * @return string
     */
    public function instanceIdentifier(CarbonInterface $time = null): string
    {
        $time = is_null($time) ? Carbon::now() : clone $time;
        return $this->start($time)->startOfWeek()->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * @param SlaInterface $sla
     * @return string
     */
    public function instanceIdentifierForSla(SlaInterface $sla): string
    {
        $time = (clone $sla->range_start)->startOfWeek();
        return $time->format('Y-m-d\TH:i:s\Z');
    }

}
