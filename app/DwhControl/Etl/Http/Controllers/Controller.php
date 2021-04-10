<?php

namespace App\DwhControl\Etl\Http\Controllers;


use App\DwhControl\Api\Http\Controllers\ControllerInterface;

abstract class Controller extends \App\DwhControl\Api\Http\Controllers\Controller implements ControllerInterface
{

    /**
     * @var string
     */
    protected string $package = 'Etl';
}
