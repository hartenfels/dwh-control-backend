<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Sla\Models\Abstract\SlaStatisticAbstract;
use App\DwhControl\Sla\Models\Interfaces\SlaInterface;
use App\DwhControl\Sla\Models\Interfaces\SlaProgressInterface;
use App\DwhControl\Sla\Traits\SlaTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class AvailabilitySlaStatistic extends SlaStatisticAbstract
{

    use SlaTypes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_id', 'progress_history', 'achievement_history'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'progress_history' => 'array',
        'achievement_history' => 'array'
    ];

    /**
     * @return $this
     */
    public function calculate(): self
    {
        $this->calculateProgress();
        $this->calculateHistory();

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
        $bucket_size = config('dwh_control.availability_sla_progress_history_bucket_size_min', 30);

        do {
            $progress_history[] = $progress->filter(fn(SlaProgressInterface $p) => $p->time->lt($cursor))->first()?->progress_percent;
        } while($cursor->addMinutes($bucket_size)->lt(clone($this->sla->range_end)->addMinutes($bucket_size)));


        $this->progress_history = $progress_history;

        if ($save) $this->save();

        return $this;
    }

    /**
     * @param int|null $count
     * @param bool $save
     * @return $this
     */
    public function calculateHistory(int $count = null, bool $save = true): self
    {
        /** @var Collection<SlaInterface> $slas */
        $slas = AvailabilitySla::where('type', $this->sla->type)
            ->where('sla_definition_id', $this->sla->sla_definition_id)
            ->where('range_end', '<=', $this->sla->range_end)
            ->orderBy('range_start', 'desc')
            ->get();

        $this->achievement_history = $slas->reverse()->map(function (SlaInterface $sla) {
            return [
                'sla_id' => $sla->id,
                'day' => $sla->range_end,
                'status' => $sla->status,
                'achieved_percent' => $sla->achieved_progress_percent,
                'target_percent' => $sla->target_percent
            ];
        })->values()->sortBy('start');

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
