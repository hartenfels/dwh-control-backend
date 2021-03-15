<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Sla\Models\Abstract\SlaStatisticAbstract;
use App\EtlMonitor\Sla\Traits\SlaTypes;

class SlaStatistic extends SlaStatisticAbstract
{

    use SlaTypes;

    /**
     * @param array $attributes
     * @param null $connection
     * @return SlaStatistic
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        if (!isset($attributes->type) || get_called_class() !== SlaStatistic::class) {
            return parent::newFromBuilder($attributes, $connection);
        }

        if (is_null($class = static::sla_types()->{$attributes->type}->statistic)) {
            throw new \InvalidArgumentException('Invalid SLA type');
        }

        $model = (new $class)->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    public function calculate(): Interfaces\SlaStatisticInterface
    {
        // TODO: Implement calculate() method.
    }
}
