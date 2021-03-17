<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Sla\Exceptions\InvalidParentCallException;
use App\EtlMonitor\Sla\Models\Abstract\SlaAbstract;
use App\EtlMonitor\Sla\Traits\SlaTypes;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class Sla extends SlaAbstract
{
    use SlaTypes;

    /**
     * @param Builder $builder
     * @param CarbonInterface $start
     * @param CarbonInterface|null $end
     * @return Builder
     */
    public function scopeInRange(Builder $builder, CarbonInterface $start, CarbonInterface $end = null): Builder
    {
        return $builder->where(function (Builder $builder) use ($start, $end) {
            return $builder->orWhere(function (Builder $b) use ($start) {
                // Start withing range
                return $b->where('range_start', '<=', $start)
                    ->where('range_end', '>=', $start);
            })->orWhere(function (Builder $b) use ($end) {
                // End within range
                return $b->where('range_start', '<=', $end)
                    ->where('range_end', '>=', $end);
            })->orWhere(function (Builder $b) use ($start, $end) {
                // Start and end within range
                return $b->where('range_start', '>=', $start)
                    ->where('range_end', '<=', $end);
            });
        });
    }

    /**
     * @param array $attributes
     * @param null $connection
     * @return Sla
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        if (!isset($attributes->type) || get_called_class() !== Sla::class) {
            return parent::newFromBuilder($attributes, $connection);
        }

        if (is_null($class = static::sla_types()->{$attributes->type}->sla)) {
            throw new \InvalidArgumentException('Invalid SLA type');
        }

        $model = (new $class)->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    /**
     * @param CarbonInterface|null $time
     * @return Interfaces\SlaInterface
     * @throws InvalidParentCallException
     */
    public function calculate(CarbonInterface $time = null): Interfaces\SlaInterface
    {
        throw new InvalidParentCallException('This method needs to be called on a child class');
    }

    /**
     * @return Interfaces\SlaInterface
     * @throws InvalidParentCallException
     */
    public function fetchProgress(): Interfaces\SlaInterface
    {
        throw new InvalidParentCallException('This method needs to be called on a child class');
    }
}
