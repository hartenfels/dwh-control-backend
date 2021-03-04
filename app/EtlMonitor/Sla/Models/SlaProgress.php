<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Sla\Models\Interfaces\SlaProgressInterface;

abstract class SlaProgress extends Model implements SlaProgressInterface
{

    /**
     * @var string
     */
    protected $table = 'sla_progress';

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_id', 'type', 'is_override',
        'time', 'progress_percent', 'source'
    ];

    protected $dates = [
        'time'
    ];

    /**
     * @var string
     */
    protected static string $type = '';

    /**
     * @return $this
     */
    public function setOverride(): self
    {
        $this->is_override = true;

        return $this;
    }

}
