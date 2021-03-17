<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Common\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaAchievementCondition extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_id', 'condition'
    ];

    /**
     * @return BelongsTo
     */
    public function sla(): BelongsTo
    {
        return $this->belongsTo(Sla::class, 'sla_id');
    }

}
