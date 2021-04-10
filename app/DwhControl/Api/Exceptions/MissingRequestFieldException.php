<?php

namespace App\DwhControl\Api\Exceptions;

class MissingRequestFieldException extends Exception
{

    public function __construct(string $field)
    {
        parent::__construct(sprintf("Field [%s] is missing in request", $field));
    }

}
