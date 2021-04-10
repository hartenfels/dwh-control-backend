<?php

namespace App\DwhControl\Etl\Models\Interfaces;

use App\DwhControl\Common\Models\Interfaces\ModelInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface EtlDefinitionStatisticInterface extends ModelInterface
{

    /**
     * @return BelongsTo
     */
    public function definition(): BelongsTo;

}
