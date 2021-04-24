<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Common\Models\Model;

class SlaDefinitionTag extends Model
{

    protected $fillable = [
        'name', 'color', 'icon', 'hide_name'
    ];

}
