<?php

namespace App\DwhControl\Api\Exceptions;

class UnhandleableRelationshipException extends Exception
{

    public function __construct(string $rel)
    {
        parent::__construct(sprintf("Relations of type [%s] can not be handled automatically.", $rel));
    }

}
