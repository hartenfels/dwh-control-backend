<?php

namespace App\EtlMonitor\Api\Attributes;

use Attribute;

#[Attribute]
class CustomAction
{

    public $action;

    public function __construct(string $action)
    {
        $this->action = $action;
    }

}
