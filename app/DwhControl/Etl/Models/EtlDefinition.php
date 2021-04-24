<?php

namespace App\DwhControl\Etl\Models;

use App\DwhControl\Etl\Models\Abstract\EtlDefinitionAbstract;
use App\DwhControl\Etl\Models\Interfaces\EtlDefinitionInterface;
use App\DwhControl\Etl\Models\Interfaces\EtlExecutionInterface;
use App\DwhControl\Etl\Traits\EtlTypes;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Matchory\Elasticsearch\Query;

class EtlDefinition extends EtlDefinitionAbstract
{
    use EtlTypes;

    /**
     * @param array $attributes
     * @param null $connection
     * @return EtlDefinition
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        if (!isset($attributes->type) || get_called_class() !== EtlDefinition::class) {
            return parent::newFromBuilder($attributes, $connection);
        }

        if (is_null($class = static::etl_types()->{$attributes->type}->definition)) {
            throw new \InvalidArgumentException('Invalid ETL type');
        }

        $model = (new $class)->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
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
     * @param CarbonInterface $from
     * @param CarbonInterface $to
     * @param string|null $field
     * @param int|null $limit
     * @return Collection
     */
    public function getExecutions(CarbonInterface $from, CarbonInterface $to, ?string $field = null, ?int $limit = 28): Collection
    {
        // TODO: Implement getExecutions() method.
    }

    /**
     * @param string|null $field
     * @return EtlExecutionInterface
     */
    public function getLatestExecution(?string $field = 'date.end_pp'): EtlExecutionInterface
    {
        // TODO: Implement getLatestExecution() method.
    }

    /**
     * @param CarbonInterface $from
     * @param CarbonInterface $to
     * @param null $field
     * @param int $limit
     * @return Collection
     */
    public function getSuccessfulExecutions(CarbonInterface $from, CarbonInterface $to, $field = null, $limit = 28): Collection
    {
        // TODO: Implement getSuccessfulExecutions() method.
    }

}
