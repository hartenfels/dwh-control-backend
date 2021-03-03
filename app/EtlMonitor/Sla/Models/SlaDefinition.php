<?php

namespace App\EtlMonitor\Sla\Models;

use App\EtlMonitor\Common\Models\Model;
use App\EtlMonitor\Sla\Models\Interfaces\SlaDefinitionInterface;
use App\EtlMonitor\Sla\Models\Interfaces\SlaInterface;
use App\EtlMonitor\Sla\Models\Interfaces\TimerangeInterface;
use App\EtlMonitor\Sla\Traits\SlaTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class SlaDefinition extends Model implements SlaDefinitionInterface
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
        'name', 'type'
    ];

    /**
     * @var string
     */
    protected static string $type = '';

    /**
     * @return HasMany
     */
    public function daily_timeranges(): HasMany
    {
        return $this->hasMany(DailyTimerange::class, 'sla_definition_id', 'id');
    }

    /**
     * @return Collection
     */
    public function timeranges(): Collection
    {
        return $this->daily_timeranges()->get();
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
            'range_start' => $timerange->start($time),
            'range_end' => $timerange->end($time)
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
     * @param array $attributes
     * @param false $exists
     * @return mixed
     */
    public function newInstance($attributes = [], $exists = false): mixed
    {
        if (is_null($class = static::sla_types()->{static::$type}->definition)) {
            throw new \InvalidArgumentException('Invalid SLA type');
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
        self::deleting(function ($definition) {
            $definition->timeranges()->each(function ($timerange) { $timerange->delete(); });
            $definition->slas->each(function ($sla) { $sla->delete(); });
        });
    }
}
