<?php

namespace App\EtlMonitor\Sla\Models\Interfaces;

use App\EtlMonitor\Common\Models\ModelInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

interface SlaDefinitionInterface extends ModelInterface
{

    /**
     * @return HasMany
     */
    public function slas(): HasMany;

    /**
     * @return HasMany
     */
    public function daily_timeranges(): HasMany;

    /**
     * @return HasMany
     */
    public function weekly_timeranges(): HasMany;

    /**
     * @param TimerangeInterface $timerange
     * @param Carbon|null $time
     * @return SlaInterface
     */
    public function createSla(TimerangeInterface $timerange, Carbon $time = null): SlaInterface;

    /**
     * @return Collection<TimerangeInterface>
     */
    public function timeranges(): Collection;

}
