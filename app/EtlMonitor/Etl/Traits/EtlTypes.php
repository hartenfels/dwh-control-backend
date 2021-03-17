<?php

namespace App\EtlMonitor\Etl\Traits;

use App\EtlMonitor\Etl\Models\AutomicEtlDefinition;
use App\EtlMonitor\Etl\Models\AutomicEtlExecution;

trait EtlTypes {

    /**
     * @return object
     */
    protected static function etl_types(): object
    {
        return (object)[
            'automic' => (object)[
                'definition' => AutomicEtlDefinition::class,
                'execution' => AutomicEtlExecution::class
            ]
        ];
    }

}
