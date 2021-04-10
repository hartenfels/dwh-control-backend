<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Common\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaAchievementCondition extends Model
{

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_id', 'condition_model', 'condition_id', 'condition_status'
    ];

    /**
     * @return BelongsTo
     */
    public function sla(): BelongsTo
    {
        return $this->belongsTo(Sla::class, 'sla_id');
    }

}
