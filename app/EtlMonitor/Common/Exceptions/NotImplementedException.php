<?php

namespace App\EtlMonitor\Common\Exceptions;

class NotImplementedException extends Exception
{

    public function __construct(string $feature)
    {
        parent::__construct(sprintf("Feature %s not yet implemented", $feature));
    }

}
