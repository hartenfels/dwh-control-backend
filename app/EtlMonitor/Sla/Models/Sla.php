<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Sla\Models\Interfaces\SlaDefinitionInterface;
use App\EtlMonitor\Sla\Models\Interfaces\SlaInterface;
use App\EtlMonitor\Sla\Models\Interfaces\SlaProgressInterface;
use App\EtlMonitor\Sla\Models\Interfaces\TimerangeInterface;
use App\EtlMonitor\Sla\Traits\SlaTypes;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

abstract class Sla extends Model implements SlaInterface
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
        'sla_definition_id', 'type',
        'range_start', 'range_end', 'is_open'
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'range_start', 'range_end'
    ];

    /**
     * @param Builder $builder
     * @param CarbonInterface $start
     * @param CarbonInterface|null $end
     * @return Builder
     */
    public function scopeInRange(Builder $builder, CarbonInterface $start, CarbonInterface $end = null): Builder
    {
        return $builder->where(function (Builder $b) use ($start) {
            return $b->where('range_start', '<=', $start)
                     ->where('range_end', '>=', $start);
        })->orWhere(function (Builder $b) use ($end) {
            return $b->where('range_start', '<=', $end)
                     ->where('range_end', '>=', $end);
        });
    }

    /**
     * @var string
     */
    protected static string $type = '';

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
             'source' => $source
         ]);

         if ($calculate) $this->calculate();

         return $progress;
     }

    /**
     * @param CarbonInterface|null $time
     * @param bool $calculate
     * @return Sla
     */
    public function updateProgress(CarbonInterface $time = null, bool $calculate = true): self
    {
        $time = $time ?? Carbon::now();

        if ($this->range_start->gt($time)) {
            if ($calculate) $this->calculate($time);

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
            ->orderBy('time', 'asc')
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


        $this->setProgressIntime($progress_intime, $progress_first_achieved_in_time)
            ->setProgressLate($progress_late, $progress_first_achieved_late)
            ->save();

        if ($calculate) $this->calculate($time);

        return $this;
    }

    /**
     * @param SlaProgressInterface|null $progress
     * @param SlaProgressInterface|null $progress_first_achieved
     * @return $this
     */
    public function setProgressIntime(SlaProgressInterface $progress = null, SlaProgressInterface $progress_first_achieved = null): self
    {
        $this->progress_last_intime()->associate($progress);
        $this->progress_first_intime_achieved()->associate($progress_first_achieved);

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

        return $this;
    }

    /**
     * @param SlaProgressInterface|null $progress_last_intime
     * @param SlaProgressInterface|null $progress_last_late
     * @return SlaInterface
     */
    public function setFailed(SlaProgressInterface $progress_last_intime = null, SlaProgressInterface $progress_last_late = null): self
    {
        $this->status = 'failed';
        $this->achieved_progress_percent = $progress_last_intime?->progress_percent;
        $this->last_progress_percent = $progress_last_late?->progress_percent;

        return $this;
    }

    /**
     * @return SlaInterface
     */
    public function setWaiting(): self
    {
        $this->status = 'waiting';

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
        return $this->belongsTo(static::sla_types()->{static::$type}->definition, 'sla_definition_id');
    }

    /**
     * @return HasMany
     */
    public function progress(): HasMany
    {
        return $this->hasMany(static::sla_types()->{static::$type}->progress, 'sla_id', 'id');
    }

    /**
     * @return belongsTo
     */
    public function progress_last_intime(): belongsTo
    {
        return $this->belongsTo(static::sla_types()->{static::$type}->progress, 'progress_last_intime_id');
    }

    /**
     * @return belongsTo
     */
    public function progress_first_intime_achieved(): belongsTo
    {
        return $this->belongsTo(static::sla_types()->{static::$type}->progress, 'progress_first_intime_achieved_id');
    }

    /**
     * @return belongsTo
     */
    public function progress_last_late(): belongsTo
    {
        return $this->belongsTo(static::sla_types()->{static::$type}->progress, 'progress_last_late_id');
    }

    /**
     * @return belongsTo
     */
    public function progress_first_late_achieved(): belongsTo
    {
        return $this->belongsTo(static::sla_types()->{static::$type}->progress, 'progress_first_late_achieved_id');
    }

    /**
     * @param array $attributes
     * @param false $exists
     * @return mixed
     */
    public function newInstance($attributes = [], $exists = false): mixed
    {
        if (is_null($class = static::sla_types()->{static::$type}->sla)) {
            throw new \InvalidArgumentException('Invalid SLA type');
        }

        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new $class((array) $attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        $model->setTable($this->getTable());

        $model->mergeCasts($this->casts);

        return $model;
    }

    /**
     *
     */
    public static function boot()
    {
        parent::boot();
        self::deleting(function ($sla) {
            $sla->progress->each(function ($progress) { $progress->delete(); });
        });
    }

}
