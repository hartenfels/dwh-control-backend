<?php

namespace App\EtlMonitor\Sla\Services;

use App\EtlMonitor\Common\Services\Service;
use App\EtlMonitor\Etl\Models\EtlDefinition;
use App\EtlMonitor\Etl\Models\Interfaces\EtlDefinitionInterface;
use App\EtlMonitor\Etl\Models\Interfaces\EtlExecutionInterface;
use App\EtlMonitor\Sla\Models\Interfaces\SlaInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlaEtlProgressFetcherService extends Service
{

    /**
     * SlaRetrievalService constructor.
     * @param SlaInterface $sla
     * @param SlaInterface|null $next_sla
     */
    public function __construct(private SlaInterface $sla, private ?SlaInterface $next_sla)
    {}

    /**
     * @return mixed
     */
    public function __invoke(): mixed
    {
        if ($this->sla->source != 'etl') return null;
        if (collect($this->sla->rules)->count() < 1) return null;

        $achieved = true;
        $done_at = null;
        $etls = [];
        collect($this->sla->rules)->each(function ($etl_id) use (&$achieved, &$done_at, &$etls) {
            if (!$achieved) return;

            /** @var EtlDefinitionInterface $definition */
            $definition = EtlDefinition::where('etl_id', $etl_id)->first();

            $executions = $definition->getSuccessfulExecutions(
                from: $this->sla->range_start,
                to: $this->next_sla ? $this->next_sla->range_start : Carbon::now(),
                limit: null
            );

            if ($executions->count() < 1) {
                $achieved = false;
                return;
            }

            list($etl_achieved, $etl_done_at, $etl) = $this->getEarliestSuccessfulExecution($executions);

            $etls[] = $etl;

            if (!$etl_achieved) $achieved = false;

            if (!$done_at || $done_at->lt($etl_done_at)) {
                $done_at = $etl_done_at;
            }
        });

        if (is_null($done_at)) return null;

        $this->sla->addProgress($done_at, $achieved ? 100 : 0, 'SlaEtlProgressFetcherService', true);
        $this->sla->achievement_conditions()->delete();
        foreach ($etls as $execution) {
            $this->sla->achievement_conditions()->create([
                'condition' => $execution->getId()
            ]);
        };

        return true;
    }

    /**
     * @param Collection<EtlExecutionInterface> $executions
     * @return array
     */
    private function getEarliestSuccessfulExecution(Collection $executions): array
    {
        $etl_achieved = null;
        $etl_done_at = null;
        $etl = null;
        $executions->each(function (EtlExecutionInterface $execution) use (&$etl_achieved, &$etl_done_at, &$etl) {
            if ($execution->getEnd()->gt($this->sla->range_end) && is_null($etl_achieved)) {
                $etl_achieved = false;

                if (!$etl_achieved && (is_null($etl_done_at) || $etl_done_at->gt($execution->getEnd()))) {
                    $etl_done_at = $execution->getEnd();
                    $etl = $execution;
                }
            } else {
                $etl_achieved = true;

                if (is_null($etl_done_at) || $etl_done_at->gt($execution->getEnd())) {
                    $etl_done_at = $execution->getEnd();
                    $etl = $execution;
                }
            }
        });

        return [$etl_achieved, $etl_done_at, $etl];
    }

}
