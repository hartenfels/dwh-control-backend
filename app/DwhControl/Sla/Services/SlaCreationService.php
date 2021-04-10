<?php

namespace App\DwhControl\Sla\Services;

use App\DwhControl\Common\Services\Service;
use App\DwhControl\Sla\Models\Interfaces\SlaDefinitionInterface;
use App\DwhControl\Sla\Models\Interfaces\SlaInterface;
use App\DwhControl\Sla\Models\Interfaces\TimerangeInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlaCreationService extends Service
{

    /**
     * SlaRetrievalService constructor.
     * @param SlaDefinitionInterface $definition
     * @param Carbon $time
     */
    public function __construct(private SlaDefinitionInterface $definition, private Carbon $time)
    {}

    /**
     * @return Collection
     */
    public function __invoke(): Collection
    {
        $slas = new Collection();
        $existing = $this->slas();

        $this->timeranges()->each(function (TimerangeInterface $timerange) use ($existing, &$slas) {
            $matches = $existing->filter(function(SlaInterface $sla) use ($timerange) {
                return $sla->matchesTimerange($timerange);
            });

            if ($matches->count() > 0) {
                $slas = $slas->merge($matches);
            } else {
                $slas->add($this->definition->createSla($timerange, $this->time));
            }
        });

        return $slas;
    }

    /**
     * @return Collection
     */
    private function timeranges(): Collection
    {
        return $this->definition->timeranges()->filter(fn(TimerangeInterface $timerange) => $timerange->startsOn($this->time));
    }

    /**
     * @return Collection
     */
    private function slas(): Collection
    {
        return $this->definition->slas()
            ->whereDate('range_start', $this->time)
            ->get();
    }

}
