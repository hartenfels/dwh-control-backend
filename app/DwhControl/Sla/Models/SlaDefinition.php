<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Sla\Models\Abstract\SlaDefinitionAbstract;
use App\DwhControl\Sla\Traits\SlaTypes;

class SlaDefinition extends SlaDefinitionAbstract
{

    use SlaTypes;

    /**
     * @param array $attributes
     * @param null $connection
     * @return SlaDefinition
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        if (!isset($attributes->type) || get_called_class() !== SlaDefinition::class) {
            return parent::newFromBuilder($attributes, $connection);
        }

        if (is_null($class = static::sla_types()->{$attributes->type}->definition)) {
            throw new \InvalidArgumentException('Invalid SLA type');
        }

        $model = (new $class)->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

}
