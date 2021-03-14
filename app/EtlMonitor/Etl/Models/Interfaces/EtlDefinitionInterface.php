<?php

namespace App\EtlMonitor\Etl\Models\Interfaces;

use App\EtlMonitor\Common\Models\Interfaces\ModelInterface;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

interface EtlDefinitionInterface extends ModelInterface
{

    /**
     * @param CarbonInterface $from
     * @param CarbonInterface $to
     * @param string $field
     * @param int $limit
     * @return Collection
     */
    public function getExecutions(CarbonInterface $from, CarbonInterface $to, $field = '@timestamp', $limit = 28): Collection;

    /**
     * @return HasOne
     */
    public function statistic(): HasOne;

}
