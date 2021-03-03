<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Sla\Models\Interfaces\TimerangeInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;

abstract class Timerange extends Model implements TimerangeInterface
{

    /**
     * @var string
     */
    protected $table = 'timeranges';

    /**
     * @var string[]
     */
    protected $fillable = [
        'anchor', 'range_start', 'range_end'
    ];

    /**
     * @param CarbonInterface|null $time
     * @return bool
     */
    public function inRange(CarbonInterface $time = null): bool
    {
        $time = $time ?? Carbon::now();
        return $time->isBetween($this->start($time), $this->end($time));
    }

}
