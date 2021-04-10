<?php

namespace App\DwhControl\Sla\Traits;

use App\DwhControl\Sla\Models\AvailabilitySla;
use App\DwhControl\Sla\Models\AvailabilitySlaDefinition;
use App\DwhControl\Sla\Models\AvailabilitySlaProgress;
use App\DwhControl\Sla\Models\AvailabilitySlaStatistic;
use App\DwhControl\Sla\Models\DeliverableSla;
use App\DwhControl\Sla\Models\DeliverableSlaDefinition;
use App\DwhControl\Sla\Models\DeliverableSlaProgress;
use App\DwhControl\Sla\Models\DeliverableSlaStatistic;

trait SlaTypes {

    /**
     * @return object
     */
    protected static function sla_types(): object
    {
        return (object)[
            'deliverable' => (object)[
                'definition' => DeliverableSlaDefinition::class,
                'sla' => DeliverableSla::class,
                'progress' => DeliverableSlaProgress::class,
                'statistic' => DeliverableSlaStatistic::class
            ],
            'availability' => (object)[
                'definition' => AvailabilitySlaDefinition::class,
                'sla' => AvailabilitySla::class,
                'progress' => AvailabilitySlaProgress::class,
                'statistic' => AvailabilitySlaStatistic::class
            ]
        ];
    }

}
