<?php

namespace App\EtlMonitor\Sla\Traits;

use App\EtlMonitor\Sla\Models\AvailabilitySla;
use App\EtlMonitor\Sla\Models\AvailabilitySlaDefinition;
use App\EtlMonitor\Sla\Models\AvailabilitySlaProgress;
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
            ],
            'availability' => (object)[
                'definition' => AvailabilitySlaDefinition::class,
                'sla' => AvailabilitySla::class,
                'progress' => AvailabilitySlaProgress::class
            ]
        ];
    }

}
