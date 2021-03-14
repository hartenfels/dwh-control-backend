<?php

namespace App\EtlMonitor\Sla\Models\Abstract;

use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Sla\Models\Interfaces\SlaInterface;
use App\EtlMonitor\Sla\Models\Interfaces\TimerangeInterface;
use App\EtlMonitor\Sla\Traits\TimerangeTypes;
use Carbon\Carbon;
use Carbon\CarbonInterface;

abstract class TimerangeAbstract extends Model implements TimerangeInterface
{

    use TimerangeTypes;

    /**
     * @var string
     */
    protected $table = 'timeranges';

    /**
     * @var string[]
     */
    protected $fillable = [
        'type', 'anchor', 'range_start', 'range_end', 'error_margin_minutes'
    ];

    /**
     * @var string
     */
    protected static string $type = '';

    /**
     * @param CarbonInterface|null $time
     * @return bool
     */
    public function inRange(CarbonInterface $time = null): bool
    {
        $time = $time ?? Carbon::now();
        return $time->isBetween($this->start($time), $this->end($time));
    }

    /**
     * @param CarbonInterface $day
     * @return bool
     */
    public function startsOn(CarbonInterface $day): bool
    {
        return $this->start($day)->isSameDay($day);
    }


    /**
     * @param SlaInterface $sla
     * @return bool
     */
    public function matchesSla(SlaInterface $sla): bool
    {
        return $this->instanceIdentifier($sla->range_start) == $this->instanceIdentifierForSla($sla);
    }

    /**
     * @param array $attributes
     * @param false $exists
     * @return mixed
     */
    public function newInstance($attributes = [], $exists = false): mixed
    {
        if (is_null($class = static::timerange_types()->{static::$type})) {
            throw new \InvalidArgumentException('Invalid Timerange type');
        }

        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new $class((array) $attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        $model->setTable($this->getTable());

        $model->mergeCasts($this->casts);

        return $model;
    }

}
