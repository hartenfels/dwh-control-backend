<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Common\Models\Model;

class SlaDefinitionStatus extends Model
{

    public $table = 'sla_definition_stati';

    protected $fillable = [
        'name'
    ];

}
