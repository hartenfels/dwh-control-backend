<?php

namespace App\DwhControl\Etl\Transfer;

class Anomaly
{

    public function __construct(public string $type, public float $factor)
    {}

}
