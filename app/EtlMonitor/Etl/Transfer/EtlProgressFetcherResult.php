<?php

namespace App\EtlMonitor\Etl\Transfer;

use Carbon\CarbonInterface;

class EtlProgressFetcherResult
{

    public function __construct(public CarbonInterface $time, public bool $achieved)
    {}

}
