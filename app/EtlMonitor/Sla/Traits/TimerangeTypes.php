<?php

namespace App\EtlMonitor\Sla\Traits;

use App\EtlMonitor\Sla\Models\DailyTimerange;
use App\EtlMonitor\Sla\Models\WeeklyTimerange;

trait TimerangeTypes {

    /**
     * @return object
     */
    protected static function timerange_types(): object
    {
        return (object)[
            'daily' => DailyTimerange::class,
            'weekly' => WeeklyTimerange::class
        ];
    }

}
