<?php

namespace App\DwhControl\Etl\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Matchory\Elasticsearch\Query;

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
    public function getExecutions(CarbonInterface $from, CarbonInterface $to, $field = 'date.end_pp', $limit = 28): Collection
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

    /**
     * @param Query $query
     * @return Query
     */
    public function scopeSuccessful(Query $query): Query
    {
        return $query->where('status', 1900);
    }
}
