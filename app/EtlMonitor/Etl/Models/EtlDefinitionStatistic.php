<?php

namespace App\EtlMonitor\Etl\Models;

use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Etl\Models\Interfaces\EtlDefinitionStatisticInterface;
use App\EtlMonitor\Etl\Models\Interfaces\EtlExecutionInterface;
use App\EtlMonitor\Etl\Traits\EtlTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtlDefinitionStatistic extends Model implements  EtlDefinitionStatisticInterface
{

    use EtlTypes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'etl_definition_id', 'type', 'execution_history'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'execution_history' => 'array',
    ];

    /**
     * @return $this
     */
    public function calculate(): self
    {
        $this->calculateHistory();

        return $this;
    }

    /**
     * @param bool $save
     * @return $this
     */
    public function calculateHistory(bool $save = true): self
    {
        $execution_history = [];
        $this->definition->getExecutions(Carbon::today()->subWeeks(4), Carbon::today(), limit: 28)
            ->each(function (EtlExecutionInterface $execution) use (&$execution_history) {
                $execution_history[] = (object)[
                    'start' => $execution->getStart(),
                    'end' => $execution->getEnd(),
                    'status' => $execution->getStatus(),
                    'anomaly' => $execution->getAnomaly()
                ];
            }
        );

        $this->execution_history = $execution_history;

        if ($save) $this->save();

        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function definition(): BelongsTo
    {
        return $this->belongsTo(static::etl_types()->{$this->type}->definition, 'etl_definition_id');
    }

}
