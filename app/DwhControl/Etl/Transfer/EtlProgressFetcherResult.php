<?php

namespace App\DwhControl\Etl\Transfer;

use Carbon\CarbonInterface;

class EtlProgressFetcherResult
{

    public function __construct(public CarbonInterface $time, public bool $achieved)
    {}

}
