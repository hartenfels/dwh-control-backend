<?php

namespace App\DwhControl\Sla\Models\Abstract;

use App\DwhControl\Common\Attributes\PivotAttributeNames;
use App\DwhControl\Common\Attributes\PivotModelName;
use App\DwhControl\Common\Models\Interfaces\SearchableInterface;
use App\DwhControl\Common\Models\Model;
use App\DwhControl\Common\Transfer\AutocompleteResult;
use App\DwhControl\Sla\Models\DailyTimerange;
use App\DwhControl\Sla\Models\Interfaces\SlaDefinitionInterface;
use App\DwhControl\Sla\Models\Interfaces\SlaInterface;
use App\DwhControl\Sla\Models\Interfaces\TimerangeInterface;
use App\DwhControl\Sla\Models\Sla;
use App\DwhControl\Sla\Models\SlaDefinition;
use App\DwhControl\Sla\Models\SlaDefinitionLifecycle;
use App\DwhControl\Sla\Models\SlaDefinitionStatistic;
use App\DwhControl\Sla\Models\SlaDefinitionTag;
use App\DwhControl\Sla\Models\SlaStatistic;
use App\DwhControl\Sla\Models\WeeklyTimerange;
use App\DwhControl\Sla\Traits\SlaTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

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
        'name', 'type', 'lifecycle_id', 'target_percent',
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
    protected $with = [
        'lifecycle'
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
     * @return BelongsToMany
     */
    #[PivotModelName('SlaDefinitionTagPivot')]
    #[PivotAttributeNames('sla_definition_id', 'tag_id')]
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(SlaDefinitionTag::class, 'dwh_control_sla__sla_definition_tags__tags_pivot', 'sla_definition_id', 'tag_id');
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

        $this->logDebug('Statistic calculated');

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
                ->limit(config('dwh_control.search_max_results_per_type'))->get()
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
                $d->getIcon(),
                $d->tags->toArray()
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
            'type' => $this->type,
            'timerange_id' => $timerange->id,
            'timerange_type' => $timerange->type,
            'range_start' => $timerange->start($time),
            'range_end' => $timerange->end($time),
            'error_margin_minutes' => $timerange->error_margin_minutes,
            'target_percent' => $this->target_percent,
            'source' => $this->source,
            'rules' => $this->rules
        ])->fresh();

        $this->logDebug(sprintf('Created SLA %s for timerange %s', $sla->id, $timerange->id));

        return $sla;
    }

    /**
     * @return HasMany
     */
    public function slas(): HasMany
    {
        return $this->hasMany(Sla::class, 'sla_definition_id', 'id');
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
