<?php

namespace App\EtlMonitor\Etl\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * @param string $field
     * @param int $limit
     * @return Collection<AutomicEtlExecution>
     */
    public function getExecutions(CarbonInterface $from, CarbonInterface $to, $field = 'date.activation', $limit = 28): Collection
    {
        return AutomicEtlExecution::query()
            ->where('etl_id.keyword', $this->etl_id)
            ->whereBetween($field, $from->format('c'), $to->format('c'))
            ->orderBy($field, 'desc')
            ->take($limit)
            ->get()
            ->reverse();
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'mdi-atom';
    }

}
