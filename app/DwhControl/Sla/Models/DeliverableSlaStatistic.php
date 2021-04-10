<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Sla\Models\Abstract\SlaStatisticAbstract;
use App\DwhControl\Sla\Models\Interfaces\SlaInterface;
use App\DwhControl\Sla\Traits\SlaTypes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\OutOfBoundsException;
use MathPHP\Statistics\Descriptive;

class DeliverableSlaStatistic extends SlaStatisticAbstract
{

    use SlaTypes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_id', 'average_lower', 'average_upper', 'achievement_history'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'achievement_history' => 'array',
    ];

    /**
     * @return $this
     * @throws BadDataException
     * @throws OutOfBoundsException
     */
    public function calculate(): self
    {
        $this->calculateAverage();
        $this->calculateHistory();

        return $this;
    }

    /**
     * @param int $size
     * @param bool $save
     * @return DeliverableSlaStatistic
     * @throws BadDataException
     * @throws OutOfBoundsException
     */
    public function calculateAverage(int $size = 30, bool $save = true): self
    {
        /** @var Collection<SlaInterface> $slas */
        $slas = DeliverableSla::where('type', $this->sla->type)
            ->where('sla_definition_id', $this->sla->sla_definition_id)
            ->where('range_start', '<', $this->sla->range_start)
            ->orderBy('range_start', 'desc')
            ->limit($size)->get();

        if ($slas->count() < 1) return $this;

        $achieved = $slas->filter(function (SlaInterface $sla) {
            return !is_null($sla->achieved_at);
        })->map(function (SlaInterface $sla)  {
            return $sla->achieved_at->diffInMinutes($sla->range_start);
        })->toArray();

        if (count($achieved) > 0)
        {
            $this->average_lower = Descriptive::percentile($achieved, 25);
            $this->average_upper = Descriptive::percentile($achieved, 75);
        }

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
        $count = $count ?? config('dwh_control.sla_statistic_history_count');

        /** @var Collection<SlaInterface> $slas */
        $slas = DeliverableSla::where('type', $this->sla->type)
            ->where('sla_definition_id', $this->sla->sla_definition_id)
            ->where('range_end', '<=', $this->sla->range_end)
            ->orderBy('range_start', 'desc')
            ->limit($count)
            ->get();

        $this->achievement_history = $slas->reverse()->map(function (SlaInterface $sla) {
            return [
                'sla_id' => $sla->id,
                'status' => $sla->status,
                'start' => $sla->range_start,
                'end' => $sla->range_end,
                'achieved_at' => $sla->achieved_at,
                'achieved_percent' => $sla->achieved_progress_percent,
                'target_percent' => $sla->target_percent,
                'error_margin_minutes' => $sla->error_margin_minutes
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
        return $this->belongsTo(DeliverableSla::class, 'sla_id');
    }

}
