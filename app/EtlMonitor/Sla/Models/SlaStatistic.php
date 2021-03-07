<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Sla\Models\Interfaces\SlaStatisticInterface;
use Illuminate\Database\Eloquent\Builder;

abstract class SlaStatistic extends Model implements SlaStatisticInterface
{

}
