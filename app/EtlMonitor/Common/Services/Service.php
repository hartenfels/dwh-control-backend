<?php

namespace App\EtlMonitor\Common\Services;

abstract class Service implements ServiceInterface
{

    /**
     * @return mixed
     */
    public function invoke(): mixed
    {
        return $this();
    }

    /**
     * @param ...$args
     * @return ServiceInterface
     */
    public static function make(...$args): ServiceInterface
    {
        return new static(...$args);
    }

}
