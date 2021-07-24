<?php

namespace App\DwhControl\Sla\Models\Abstract;

use App\DwhControl\Common\Models\Model;
use App\DwhControl\Sla\Models\Interfaces\SlaInterface;
use App\DwhControl\Sla\Models\Interfaces\SlaProgressInterface;
use App\DwhControl\Sla\Models\Interfaces\TimerangeInterface;
use App\DwhControl\Sla\Models\Sla;
use App\DwhControl\Sla\Models\SlaAchievementCondition;
use App\DwhControl\Sla\Models\SlaDefinition;
use App\DwhControl\Sla\Models\SlaProgress;
use App\DwhControl\Sla\Models\SlaStatistic;
use App\DwhControl\Sla\Traits\SlaTypes;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\OutOfBoundsException;

abstract class SlaAbstract extends Model implements SlaInterface
{

    use SlaTypes;

    /**
     * @var string
     */
    protected $table = 'slas';

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_definition_id', 'timerange_id', 'type', 'timerange_type', 'error_margin_minutes',
        'range_start', 'range_end', 'is_open', 'target_percent',
        'source', 'rules'
    ];

    /**
     * @var array|string[]|null
     */
    protected ?array $transformable = [
        'id', 'sla_definition_id', 'timerange_id', 'type', 'timerange_type',
        'target_percent', 'range_start', 'range_end', 'achieved_at',
        'status', 'target_percent', 'is_open', 'error_margin_minutes',
        'achieved_progress_percent', 'last_progress_percent',
        'progress_last_intime_id', 'progress_first_intime_achieved_id',
        'progress_last_late_id', 'progress_first_late_achieved_id',
        'statistics_average_duration_minutes_lower', 'statistics_average_duration_minutes_upper',
        'source', 'rules'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'rules' => 'array'
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'range_start', 'range_end', 'achieved_at'
    ];

    /**
     * @var string
     */
    protected static string $type = '';

    /**
     * @return SlaInterface|null
     */
    public function next(): ?SlaInterface
    {
        return $this->definition->slas()->where('range_start', '>', $this->range_end)->orderBy('range_start')->first();
    }

    /**
     * @param CarbonInterface $time
     * @param float $progress_percent
     * @param string $source
     * @param bool $calculate
     * @return SlaProgressInterface
     */
    public function addProgress(CarbonInterface $time, float $progress_percent, string $source, bool $calculate = false): SlaProgressInterface
     {
         /** @var SlaProgressInterface $progress */
         $progress = $this->progress()->create([
             'time' => $time,
             'progress_percent' => $progress_percent,
             'source' => $source,
             'type' => $this->type
         ]);

         $this->logDebug(sprintf('Received progress: progress: %s, percent: %s, source: %s',
             $progress->id, $progress_percent, $source));

         if ($calculate) $this->calculate();

         return $progress;
     }

    /**
     * @param CarbonInterface|null $time
     * @param bool $calculate
     * @param bool $fetch
     * @return Sla
     */
    public function updateProgress(CarbonInterface $time = null, bool $calculate = true, bool $fetch = true): self
    {
        if ($fetch) $this->fetchProgress();
        $time = $time ?? Carbon::now();

        if ($this->range_start->gt($time)) {
            if ($calculate) $this->calculate($time);

            $this->logDebug(sprintf('SLA progress with time %s skipped due to time constraints', $time->format('c')));
            return $this;
        }

        /** @var SlaProgressInterface $progress_intime */
        $progress_intime = $this->progress()
            ->where('time', '>=', $this->range_start)
            ->where('time', '<=', $this->range_end)
            ->orderBy('is_override', 'desc')
            ->orderBy('time', 'desc')
            ->first();

        /** @var SlaProgressInterface $progress_first_achieved_in_time */
        $progress_first_achieved_in_time = $this->progress()
            ->where('time', '>=', $this->range_start)
            ->where('time', '<=', $this->range_end)
            ->where('progress_percent', '>=', $this->target_percent)
            ->orderBy('is_override', 'desc')
            ->orderBy('time')
            ->first();

        /** @var SlaProgressInterface $progress_best_in_time */
        $progress_best_in_time = $this->progress()
            ->where('time', '>=', $this->range_start)
            ->where('time', '<=', $this->range_end)
            ->orderBy('is_override', 'desc')
            ->orderBy('progress_percent', 'desc')
            ->first();

        /** @var SlaProgressInterface $progress_late */
        $progress_late = $this->progress()
            ->where('time', '>', $this->range_end)
            ->orderBy('is_override', 'desc')
            ->orderBy('time', 'desc')
            ->first();

        /** @var SlaProgressInterface $progress_first_achieved_late */
        $progress_first_achieved_late = $this->progress()
            ->where('time', '>', $this->range_end)
            ->where('progress_percent', '>=', $this->target_percent)
            ->orderBy('is_override', 'desc')
            ->orderBy('time', 'asc')
            ->first();


        $this->setProgressIntime($progress_intime, $progress_first_achieved_in_time, $progress_best_in_time)
            ->setProgressLate($progress_late, $progress_first_achieved_late)
            ->save();

        $this->logDebug(sprintf('Fetched and processed SLA progress. intime: %s, first_achieved_in_time: %s, best_in_time: %s, late: %s, first_achieved_late: %s',
            $progress_intime?->progress_percent, $progress_first_achieved_in_time?->progress_percent, $progress_best_in_time?->progress_percent,
            $progress_late?->progress_percent, $progress_first_achieved_late?->progress_percent));

        if ($calculate) $this->calculate($time);

        return $this;
    }

    /**
     * @param SlaProgressInterface|null $progress
     * @param SlaProgressInterface|null $progress_first_achieved
     * @param SlaProgressInterface|null $progress_best_in_time
     * @return $this
     */
    public function setProgressIntime(
        SlaProgressInterface $progress = null,
        SlaProgressInterface $progress_first_achieved = null,
        SlaProgressInterface $progress_best_in_time = null
    ): self
    {
        $this->progress_last_intime()->associate($progress);
        $this->progress_first_intime_achieved()->associate($progress_first_achieved);
        $this->progress_best_intime()->associate($progress_best_in_time);

        return $this;
    }

    /**
     * @param SlaProgressInterface|null $progress
     * @param SlaProgressInterface|null $progress_first_achieved
     * @return $this
     */
    public function setProgressLate(SlaProgressInterface $progress = null, SlaProgressInterface $progress_first_achieved = null): self
    {
        $this->progress_last_late()->associate($progress);
        $this->progress_first_late_achieved()->associate($progress_first_achieved);

        return $this;
    }

    /**
     * @param SlaProgressInterface $progress
     * @return SlaInterface
     */
    public function setAchieved(SlaProgressInterface $progress): self
    {
        $this->status = 'achieved';
        $this->achieved_progress_percent = $progress->progress_percent;
        $this->last_progress_percent = null;
        $this->achieved_at = $progress->time;

        return $this;
    }

    /**
     * @param SlaProgressInterface|null $progress_last_intime
     * @param SlaProgressInterface|null $progress_last_late
     * @param SlaProgressInterface|null $progress_achieved_late
     * @return SlaInterface
     */
    public function setFailed(SlaProgressInterface $progress_last_intime = null, SlaProgressInterface $progress_last_late = null, SlaProgressInterface $progress_achieved_late = null): self
    {
        $this->status = 'failed';
        $this->achieved_progress_percent = $progress_last_intime?->progress_percent;
        $this->last_progress_percent = $progress_last_late?->progress_percent;
        $this->achieved_at = $progress_last_late?->time;

        return $this;
    }

    /**
     * @param SlaProgressInterface|null $progress_last_intime
     * @return Sla
     */
    public function setWaiting(SlaProgressInterface $progress_last_intime = null): self
    {
        $this->status = 'waiting';
        $this->achieved_progress_percent = $progress_last_intime?->progress_percent;
        $this->last_progress_percent = null;
        $this->achieved_at = null;

        return $this;
    }

    /**
     * @return SlaInterface
     */
    public function setLate(): self
    {
        $this->status = 'late';
        $this->achieved_progress_percent = null;
        $this->last_progress_percent = null;
        $this->achieved_at = null;

        return $this;
    }

    /**
     * @return SlaInterface
     */
    public function setClosed(): self
    {
        $this->is_open = false;

        return $this;
    }

    /**
     * @param TimerangeInterface $timerange
     * @return bool
     */
    public function matchesTimerange(TimerangeInterface $timerange): bool
    {
        return $timerange->matchesSla($this);
    }

    /**
     * @return BelongsTo
     */
    public function definition(): BelongsTo
    {
        return $this->belongsTo(SlaDefinition::class, 'sla_definition_id');
    }

    /**
     * @return HasOne
     */
    public function statistic(): HasOne
    {
        return $this->hasOne(SlaStatistic::class, 'sla_id');
    }

    /**
     * @return HasMany
     */
    public function progress(): HasMany
    {
        return $this->hasMany(SlaProgress::class, 'sla_id', 'id');
    }

    /**
     * @return belongsTo
     */
    public function progress_last_intime(): belongsTo
    {
        return $this->belongsTo(SlaProgress::class, 'progress_last_intime_id');
    }

    /**
     * @return belongsTo
     */
    public function progress_first_intime_achieved(): belongsTo
    {
        return $this->belongsTo(SlaProgress::class, 'progress_first_intime_achieved_id');
    }

    /**
     * @return belongsTo
     */
    public function progress_best_intime(): belongsTo
    {
        return $this->belongsTo(SlaProgress::class, 'progress_best_intime_id');
    }

    /**
     * @return belongsTo
     */
    public function progress_last_late(): belongsTo
    {
        return $this->belongsTo(SlaProgress::class, 'progress_last_late_id');
    }

    /**
     * @return belongsTo
     */
    public function progress_first_late_achieved(): belongsTo
    {
        return $this->belongsTo(SlaProgress::class, 'progress_first_late_achieved_id');
    }

    /**
     * @return HasMany
     */
    public function achievement_conditions(): HasMany
    {
        return $this->hasMany(SlaAchievementCondition::class, 'sla_id');
    }

    /**
     * @return $this
     * @throws BadDataException
     * @throws OutOfBoundsException
     */
    public function calculateStatistics(): self
    {
        /** @var SlaStatistic $statistic */
        if (is_null($this->statistic)) {
            $statistic = $this->statistic()->create(['type' => $this->type])->fresh();
            $this->fresh();
        } else {
            $statistic = $this->statistic;
        }

        $statistic->calculate();

        $this->logDebug('Statistic calculated');

        return $this;
    }

    /**
     *
     */
    public static function boot()
    {
        parent::boot();

        if (get_called_class() != Sla::class) {
            static::addGlobalScope('type', function (Builder $builder) {
                $builder->where('type', static::$type);
            });
        }

        self::deleting(function ($sla) {
            $sla->progress->each(function (SlaProgressInterface $progress) { $progress->delete(); });
            $sla->statistic->delete();
        });
    }
}
