<?php

namespace App\DwhControl\Etl\Models;

use App\DwhControl\Etl\Models\Interfaces\EtlExecutionInterface;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class AutomicEtlDefinition extends EtlDefinition
{

    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'automic'
    ];

    /**
     * @var string
     */
    protected static string $type = 'automic';

    /**
     * @param CarbonInterface $from
     * @param CarbonInterface $to
     * @param string|null $field
     * @param int|null $limit
     * @return Collection<AutomicEtlExecution>
     */
    public function getExecutions(CarbonInterface $from, CarbonInterface $to, ?string $field = 'date.end_pp', ?int $limit = 28): Collection
    {
        $q = AutomicEtlExecution::query()
            ->where('etl_id.keyword', $this->etl_id)
            ->whereBetween($field, $from->format('c'), $to->format('c'))
            ->orderBy($field, 'desc');

        if (!is_null($limit)) {
            $q = $q->take($limit);
        }

        $q = $q->get()->reverse();

        return $q;
    }

    /**
     * @param string|null $field
     * @return EtlExecutionInterface|null
     */
    public function getLatestExecution(?string $field = 'date.end_pp'): ?EtlExecutionInterface
    {
        return AutomicEtlExecution::where('etl_id.keyword', $this->etl_id)
            ->orderBy($field, 'desc')
            ->take(1)
            ->first();
    }

    /**
     * @param CarbonInterface $from
     * @param CarbonInterface $to
     * @param null $field
     * @param int $limit
     * @return Collection
     */
    public function getSuccessfulExecutions(CarbonInterface $from, CarbonInterface $to, $field = 'date.end', $limit = 28): Collection
    {
        $q = AutomicEtlExecution::query()
            ->where('etl_id.keyword', $this->etl_id)
            ->where('status', 1900)
            ->whereBetween($field, $from->format('c'), $to->format('c'))
            ->orderBy($field, 'desc');

        if (!is_null($limit)) {
            $q = $q->take($limit);
        }

        $q = $q->get()->reverse();

        return $q;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'mdi-atom';
    }

}
