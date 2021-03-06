<?php

namespace App\EtlMonitor\Sla\Models\Interfaces;

interface SlaStatisticInterface
{

    public function calculate(): self;

}
