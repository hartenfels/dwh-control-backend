<?php

namespace App\DwhControl\Sla\Models\Interfaces;

use App\DwhControl\Common\Models\Interfaces\ModelInterface;

interface SlaProgressInterface extends ModelInterface
{

    /**
     * @return $this
     */
    public function setOverride(): self;

}
