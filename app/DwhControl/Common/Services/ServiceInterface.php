<?php

namespace App\DwhControl\Common\Services;

interface ServiceInterface
{

    /**
     * @param ...$args
     * @return static
     */
    public static function make(...$args): self;

    /**
     * @return mixed
     */
    public function invoke(): mixed;

    /**
     * @return mixed
     */
    public function __invoke(): mixed;

}
