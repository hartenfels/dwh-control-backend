<?php

namespace App\DwhControl\Etl\Models\Abstract;

use App\DwhControl\Common\Attributes\PivotAttributeNames;
use App\DwhControl\Common\Attributes\PivotModelName;
use App\DwhControl\Common\Models\Interfaces\SearchableInterface;
use App\DwhControl\Common\Models\Model;
use App\DwhControl\Common\Transfer\AutocompleteResult;
use App\DwhControl\Etl\Models\EtlDefinition;
use App\DwhControl\Etl\Models\EtlDefinitionStatistic;
use App\DwhControl\Etl\Models\Interfaces\EtlDefinitionInterface;
use App\DwhControl\Etl\Traits\EtlTypes;
use App\DwhControl\Sla\Models\SlaDefinition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

abstract class EtlDefinitionAbstract extends Model implements EtlDefinitionInterface, SearchableInterface
{

    use EtlTypes;

    /**
     * @var string
     */
    protected $table = 'etl_definitions';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'etl_id'
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
     * @return BelongsToMany
     */
    #[PivotModelName('EtlDefinitionAffectedSlaPivot')]
    #[PivotAttributeNames('etl_definition_id', 'sla_definition_id')]
    public function affected_slas(): BelongsToMany
    {
        return $this->belongsToMany(SlaDefinition::class, 'dwh_control_sla__sla_definition_affecting_etls', 'sla_definition_id', 'etl_definition_id');
    }

    /**
     * @return EtlDefinitionInterface
     */
    public function updateFromExecution(): EtlDefinitionInterface
    {
        if (!$this->update_from_execution) return $this;

        $execution = $this->getLatestExecution();

        if (is_null($execution)) return $this;

        foreach (config('dwh_control.etl_execution_mapping.' . static::$type . '.fields', []) as $d=>$e) {
            $this->$d = $execution->$e;
        }

        $references_field = config('dwh_control.etl_execution_mapping.' . static::$type . '.depends_on.references_field');
        $depends_on_field = config('dwh_control.etl_execution_mapping.' . static::$type . '.depends_on.depends_on_field');

        if (!is_null($execution->$depends_on_field)) {
            $execution_type = static::etl_types()->{static::$type}->execution;

            $depends_on_ids = is_array($execution->$depends_on_field) ? $execution->$depends_on_field : [$execution->$depends_on_field];
            $depends_on_executions = $execution_type::query()->whereIn($references_field, $depends_on_ids)->get();

            if ($depends_on_executions->count() < 1) {
                $this->depends_on()->sync([]);
            } else {
                $etl_id_field = config('dwh_control.etl_execution_mapping.' . static::$type . '.fields.etl_id');
                $etl_ids = $depends_on_executions->map(function ($e) use ($etl_id_field) {
                    return $e->$etl_id_field;
                });

                $depends_on_definitions = static::query()->whereIn('etl_id', $etl_ids)->get();

                $this->depends_on()->sync($depends_on_definitions->map(fn ($d) => $d->id));
            }
        } else {
            $this->depends_on()->sync([]);
        }

        $this->save();

        return $this->fresh();
    }

    /**
     * @return EtlDefinitionStatistic
     */
    public function calculateStatistic(): EtlDefinitionStatistic
    {
        /** @var EtlDefinitionStatistic $statistic */
        $statistic = null;
        if (!$this->statistic) {
            $statistic = $this->statistic()->create([
                'type' => $this->type
            ]);
        } else {
            $statistic = $this->statistic;
        }

        return $statistic->calculate();
    }

    /**
     * @param string $search_text
     * @return Collection
     */
    public static function autocomplete(string $search_text): Collection
    {
        $definitions = new Collection();
        $sql_filter = '%' . $search_text . '%';

        foreach (get_object_vars(static::etl_types()) as $type=>$n) {
            static::etl_types()->$type->definition::where('name', 'like', $sql_filter)
                ->limit(config('dwh_control.search_max_results_per_type'))->get()
                ->each(function (EtlDefinitionInterface $d) use (&$definitions) {
                    $definitions->push($d);
                });
        }

        $sla_definition_collection = new Collection();
        $definitions->each(function (EtlDefinitionInterface $d) use (&$sla_definition_collection) {
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
     * @return HasOne
     */
    public function statistic(): HasOne
    {
        return $this->hasOne(EtlDefinitionStatistic::class, 'etl_definition_id');
    }

    /**
     * @return BelongsToMany
     */
    #[PivotModelName('EtlDefinitionDependsonPivot')]
    #[PivotAttributeNames('etl_definition_id', 'dependson_etl_definition_id')]
    public function depends_on(): BelongsToMany
    {
        return $this->belongsToMany(EtlDefinition::class, 'dwh_control_etl__etl_definitions_dependson_pivot', 'etl_definition_id', 'dependson_etl_definition_id');
    }

    /**
     *
     */
    public static function boot()
    {
        parent::boot();

        if (get_called_class() != EtlDefinition::class) {
            static::addGlobalScope('type', function (Builder $builder) {
                $builder->where('type', static::$type);
            });
        }
    }
}
