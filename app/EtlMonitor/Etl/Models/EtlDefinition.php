<?php

namespace App\EtlMonitor\Etl\Models;

use App\EtlMonitor\Etl\Models\Abstract\EtlDefinitionAbstract;
use App\EtlMonitor\Etl\Traits\EtlTypes;
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

    public function getExecutions(CarbonInterface $from, CarbonInterface $to, $field = null, $limit = 28): Collection
    {
        // TODO: Implement getExecutions() method.
    }

    public function getSuccessfulExecutions(CarbonInterface $from, CarbonInterface $to, $field = null, $limit = 28): Collection
    {
        // TODO: Implement getSuccessfulExecutions() method.
    }

    public function scopeSuccessful(Query $query): Query
    {
        // TODO: Implement scopeSuccessful() method.
    }
}
