<?php

namespace App\DwhControl\Etl\Models\Interfaces;

use App\DwhControl\Common\Models\Interfaces\ModelInterface;
use App\DwhControl\Etl\Transfer\Anomaly;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

interface EtlExecutionInterface extends ModelInterface
{

    /**
     * @return CarbonInterface
     */
    public function getStart(): CarbonInterface;

    /**
     * @return CarbonInterface
     */
    public function getEnd(): CarbonInterface;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return Collection<Anomaly>
     */
    public function getAnomaly(): Collection;

}
