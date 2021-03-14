<?php

namespace App\EtlMonitor\Etl\Models;

use App\EtlMonitor\Common\Models\Interfaces\SearchableInterface;
use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Common\Transfer\AutocompleteResult;
use App\EtlMonitor\Etl\Models\Interfaces\EtlDefinitionInterface;
use App\EtlMonitor\Etl\Traits\EtlTypes;
use App\EtlMonitor\Sla\Models\Interfaces\SlaProgressInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

abstract class EtlDefinition extends Model implements EtlDefinitionInterface, SearchableInterface
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
     * @var string[]
     */
    protected $with = [
        'statistic'
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
                ->limit(config('etl_monitor.search_max_results_per_type'))->get()
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
     * @param array $attributes
     * @param false $exists
     * @return mixed
     */
    public function newInstance($attributes = [], $exists = false): mixed
    {
        if (is_null($class = static::etl_types()->{static::$type}->definition)) {
            throw new \InvalidArgumentException('Invalid ETL type');
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

        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', static::$type);
        });

        self::deleting(function ($sla) {
            $sla->progress->each(function (SlaProgressInterface $progress) { $progress->delete(); });
            $sla->statistic->delete();
        });
    }
}
