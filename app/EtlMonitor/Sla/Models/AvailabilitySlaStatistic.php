<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Sla\Models\Interfaces\SlaProgressInterface;
use App\EtlMonitor\Sla\Traits\SlaTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilitySlaStatistic extends SlaStatistic
{

    use SlaTypes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_id', 'progress_history'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'progress_history' => 'array',
    ];

    /**
     * @return $this
     */
    public function calculate(): self
    {
        $this->calculateProgress();

        return $this;
    }

    /**
     * @param bool $save
     * @return $this
     */
    public function calculateProgress(bool $save = true): self
    {
        /** @var Carbon $cursor */
        $cursor = $this->sla->range_start;
        $progress = $this->sla->progress()->orderBy('time', 'desc')->get();
        $progress_history = [];

        do {
            $progress_history[] = $progress->filter(fn(SlaProgressInterface $p) => $p->time->lt($cursor))->first()?->progress_percent;
        } while($cursor->addMinutes(config('etl_monitor.availability_sla_progress_history_bucket_size_min', 60))->lt($this->sla->range_end));


        $this->progress_history = $progress_history;

        if ($save) $this->save();

        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function sla(): BelongsTo
    {
        return $this->belongsTo(AvailabilitySla::class, 'sla_id');
    }

}
