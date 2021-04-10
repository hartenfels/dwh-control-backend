<?php

namespace App\DwhControl\Sla\Http\Controllers;


use App\DwhControl\Api\Http\Controllers\ControllerInterface;

abstract class Controller extends \App\DwhControl\Api\Http\Controllers\Controller implements ControllerInterface
{

    /**
     * @var string
     */
    protected string $package = 'Sla';
}
