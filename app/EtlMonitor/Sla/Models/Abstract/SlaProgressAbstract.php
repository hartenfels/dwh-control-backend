<?php

namespace App\EtlMonitor\Sla\Models\Abstract;

use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Sla\Models\Interfaces\SlaProgressInterface;
use App\EtlMonitor\Sla\Models\SlaProgress;
use Illuminate\Database\Eloquent\Builder;

abstract class SlaProgressAbstract extends Model implements SlaProgressInterface
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

    /**
     *
     */
    public static function boot()
    {
        parent::boot();
        if (get_called_class() != SlaProgress::class) {
            static::addGlobalScope('type', function (Builder $builder) {
                $builder->where('type', static::$type);
            });
        }
    }

}
