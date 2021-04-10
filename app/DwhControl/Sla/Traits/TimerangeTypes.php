<?php

namespace App\DwhControl\Sla\Traits;

use App\DwhControl\Sla\Models\DailyTimerange;
use App\DwhControl\Sla\Models\WeeklyTimerange;

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
