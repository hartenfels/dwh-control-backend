<?php

namespace App\DwhControl\Etl\Traits;

use App\DwhControl\Etl\Models\AutomicEtlDefinition;
use App\DwhControl\Etl\Models\AutomicEtlExecution;

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
