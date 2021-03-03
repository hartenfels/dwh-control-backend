<?php

namespace App\EtlMonitor\Sla\Models\Interfaces;

use App\EtlMonitor\Common\Models\ModelInterface;

interface SlaProgressInterface extends ModelInterface
{

    /**
     * @return $this
     */
    public function setOverride(): self;

}
