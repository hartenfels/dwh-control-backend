<?php

namespace App\EtlMonitor\Sla\Models\Interfaces;

use App\EtlMonitor\Common\Models\Interfaces\ModelInterface;
use Carbon\CarbonInterface;

/**
 * Interface TimerangeInterface
 * @package App\EtlMonitor\Sla\Models\Timerange
 */
interface TimerangeInterface extends ModelInterface
{

    /**
     * @param CarbonInterface|null $time
     * @return CarbonInterface
     */
    public function start(CarbonInterface $time = null): CarbonInterface;

    /**
     * @param CarbonInterface|null $time
     * @return CarbonInterface
     */
    public function end(CarbonInterface $time = null): CarbonInterface;

    /**
     * @param CarbonInterface|null $time
     * @return bool
     */
    public function inRange(CarbonInterface $time = null): bool;

    /**
     * @param CarbonInterface $day
     * @return bool
     */
    public function startsOn(CarbonInterface $day): bool;

    /**
     * @param CarbonInterface|null $time
     * @return string
     */
    public function instanceIdentifier(CarbonInterface $time = null): string;

    /**
     * @param SlaInterface $sla
     * @return bool
     */
    public function matchesSla(SlaInterface $sla): bool;

}
