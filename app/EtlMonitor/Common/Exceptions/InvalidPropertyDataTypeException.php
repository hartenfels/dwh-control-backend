<?php

namespace App\EtlMonitor\Common\Exceptions;

class InvalidPropertyDataTypeException extends Exception
{

    public function __construct(string $data_type)
    {
        parent::__construct(sprintf("Data type [%s] is not supported", $data_type));
    }

}
