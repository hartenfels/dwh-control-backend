<?php

namespace App\EtlMonitor\Sla\Traits;

use App\EtlMonitor\Sla\Models\DeliverableSla;
use App\EtlMonitor\Sla\Models\DeliverableSlaDefinition;
use App\EtlMonitor\Sla\Models\DeliverableSlaProgress;

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
                'progress' => DeliverableSlaProgress::class
            ]
        ];
    }

}
