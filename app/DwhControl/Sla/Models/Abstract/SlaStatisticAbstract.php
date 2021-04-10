<?php

namespace App\DwhControl\Sla\Models\Abstract;

use App\DwhControl\Common\Models\Model;
use App\DwhControl\Sla\Models\Interfaces\SlaStatisticInterface;
use App\DwhControl\Sla\Models\SlaStatistic;
use Illuminate\Database\Eloquent\Builder;

abstract class SlaStatisticAbstract extends Model implements SlaStatisticInterface
{

    /**
     * @var string
     */
    protected $table = 'sla_statistics';

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_id', 'type',
        'average_lower', 'average_upper',
        'progress_history', 'achievement_history'
    ];

    /**
     *
     */
    public static function boot()
    {
        parent::boot();
        if (get_called_class() != SlaStatistic::class) {
            static::addGlobalScope('type', function (Builder $builder) {
                $builder->where('type', static::$type);
            });
        }
    }

}
