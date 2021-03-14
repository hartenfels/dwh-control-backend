<?php

namespace App\EtlMonitor\Sla\Models\Abstract;

use App\EtlMonitor\Common\Models\Interfaces\SearchableInterface;
use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Common\Transfer\AutocompleteResult;
use App\EtlMonitor\Sla\Models\DailyTimerange;
use App\EtlMonitor\Sla\Models\Interfaces\SlaDefinitionInterface;
use App\EtlMonitor\Sla\Models\Interfaces\SlaInterface;
use App\EtlMonitor\Sla\Models\Interfaces\TimerangeInterface;
use App\EtlMonitor\Sla\Models\SlaDefinition;
use App\EtlMonitor\Sla\Models\SlaDefinitionLifecycle;
use App\EtlMonitor\Sla\Models\SlaDefinitionStatistic;
use App\EtlMonitor\Sla\Models\WeeklyTimerange;
use App\EtlMonitor\Sla\Traits\SlaTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\OutOfBoundsException;

abstract class SlaDefinitionAbstract extends Model implements SlaDefinitionInterface, SearchableInterface
{

    use SlaTypes;

    /**
     * @var string
     */
    protected $table = 'sla_definitions';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'type', 'lifecycle_id', 'target_percent'
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'lifecycle', 'statistic'
    ];

    /**
     * @var string
     */
    protected static string $type = '';

    /**
     * @return string
     */
    public function entity(): string
    {
        return static::$type . '_' . parent::entity();
    }

    /**
     * @return BelongsTo
     */
    public function lifecycle(): BelongsTo
    {
        return $this->belongsTo(SlaDefinitionLifecycle::class, 'lifecycle_id');
    }

    /**
     * @return HasMany
     */
    public function daily_timeranges(): HasMany
    {
        return $this->hasMany(DailyTimerange::class, 'sla_definition_id', 'id')->where('type', 'daily');
    }

    /**
     * @return HasMany
     */
    public function weekly_timeranges(): HasMany
    {
        return $this->hasMany(WeeklyTimerange::class, 'sla_definition_id', 'id')->where('type', 'weekly');
    }

    /**
     * @return Collection
     */
    public function timeranges(): Collection
    {
        return $this->daily_timeranges->merge($this->weekly_timeranges);
    }

    /**
     * @return HasOne
     */
    public function statistic(): HasOne
    {
        return $this->hasOne(SlaDefinitionStatistic::class, 'sla_definition_id');
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
            $statistic = $this->statistic()->create(['type' => $this->type]);
            $this->fresh();
        } else {
            $statistic = $this->statistic;
        }

        $statistic->calculate();

        return $this;
    }

    /**
     * @param string $search_text
     * @return Collection
     */
    public static function autocomplete(string $search_text): Collection
    {
        $definitions = new Collection();
        $sql_filter = '%' . $search_text . '%';

        foreach (get_object_vars(static::sla_types()) as $type=>$n) {
            static::sla_types()->$type->definition::where('name', 'like', $sql_filter)
                ->limit(config('etl_monitor.search_max_results_per_type'))->get()
                ->each(function (SlaDefinitionInterface $d) use (&$definitions) {
                    $definitions->push($d);
                });
        }

        $sla_definition_collection = new Collection();
        $definitions->each(function (SlaDefinitionInterface $d) use (&$sla_definition_collection) {
            $sla_definition_collection->add(new AutocompleteResult(
                $d->id,
                $d->type,
                $d->name,
                (object)['definition' => $d, 'statistic' => $d->statistic],
                $d->model(),
                $d->entity(),
                $d->getIcon()
            ));
        });

        return $sla_definition_collection;
    }

    /**
     * @param TimerangeInterface $timerange
     * @param Carbon|null $time
     * @return SlaInterface
     */
    public function createSla(TimerangeInterface $timerange, Carbon $time = null): SlaInterface
    {
        $time = $time ?? Carbon::now();

        /** @var SlaInterface $sla */
        $sla = $this->slas()->create([
            'timerange_id' => $timerange->id,
            'timerange_type' => $timerange->type,
            'range_start' => $timerange->start($time),
            'range_end' => $timerange->end($time),
            'error_margin_minutes' => $timerange->error_margin_minutes,
            'target_percent' => $this->target_percent
        ]);

        return $sla;
    }

    /**
     * @return HasMany
     */
    public function slas(): HasMany
    {
        return $this->hasMany(static::sla_types()->{$this->type}->sla, 'sla_definition_id', 'id');
    }

    /**
     *
     */
    public static function boot()
    {
        parent::boot();

        if (get_called_class() != SlaDefinition::class) {
            static::addGlobalScope('type', function (Builder $builder) {
                $builder->where('type', static::$type);
            });
        }

        self::deleting(function ($definition) {
            $definition->timeranges()->each(function ($timerange) { $timerange->delete(); });
            $definition->slas->each(function ($sla) { $sla->delete(); });
        });
    }
}
