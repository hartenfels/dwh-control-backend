<?php

namespace App\EtlMonitor\Etl\Models\Interfaces;

use App\EtlMonitor\Common\Models\Interfaces\ModelInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface EtlDefinitionStatisticInterface extends ModelInterface
{

    /**
     * @return BelongsTo
     */
    public function definition(): BelongsTo;

}
