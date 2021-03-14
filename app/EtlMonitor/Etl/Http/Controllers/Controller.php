<?php

namespace App\EtlMonitor\Etl\Http\Controllers;


use App\EtlMonitor\Api\Http\Controllers\ControllerInterface;

abstract class Controller extends \App\EtlMonitor\Api\Http\Controllers\Controller implements ControllerInterface
{

    /**
     * @var string
     */
    protected string $package = 'Etl';
}
