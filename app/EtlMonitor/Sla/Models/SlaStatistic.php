<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Sla\Models\Interfaces\SlaInterface;
use App\EtlMonitor\Sla\Traits\SlaTypes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\OutOfBoundsException;
use MathPHP\Statistics\Descriptive;

class SlaStatistic extends Model
{

    use SlaTypes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_id', 'type', 'average_duration_minutes_lower', 'average_duration_minutes_upper', 'achievement_history'
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
     * @return SlaStatistic
     * @throws BadDataException
     * @throws OutOfBoundsException
     */
    public function calculateAverage(int $size = 30, bool $save = true): self
    {
        /** @var Collection<SlaInterface> $slas */
        $slas = static::sla_types()->{$this->type}->sla::where('type', $this->sla->type)
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

        $this->average_duration_minutes_lower = Descriptive::percentile($achieved, 25);
        $this->average_duration_minutes_upper = Descriptive::percentile($achieved, 75);

        if ($save) $this->save();

        return $this;
    }

    /**
     * @param int $days
     * @param bool $save
     * @return $this
     */
    public function calculateHistory(int $days = 14, bool $save = true): self
    {
        /** @var Collection<SlaInterface> $slas */
        $slas = $this->sla->definition->slas()->where('type', $this->sla->type)
            ->where('range_start', '<', $this->sla->range_start)
            ->where('range_start', '>=', (clone $this->sla->range_start)->subDays($days)->startOfDay())
            ->orderBy('range_start', 'desc')
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
        return $this->belongsTo(static::sla_types()->{$this->type}->sla, 'sla_id');
    }

}
