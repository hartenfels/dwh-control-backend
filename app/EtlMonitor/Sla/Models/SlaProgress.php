<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Sla\Models\Abstract\SlaProgressAbstract;
use App\EtlMonitor\Sla\Traits\SlaTypes;

class SlaProgress extends SlaProgressAbstract
{

    use SlaTypes;

    /**
     * @param array $attributes
     * @param null $connection
     * @return SlaProgress
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        if (!isset($attributes->type) || get_called_class() !== SlaProgress::class) {
            return parent::newFromBuilder($attributes, $connection);
        }

        if (is_null($class = static::sla_types()->{$attributes->type}->progress)) {
            throw new \InvalidArgumentException('Invalid SLA type');
        }

        $model = (new $class)->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

}
