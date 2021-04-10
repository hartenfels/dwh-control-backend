<?php

namespace App\DwhControl\Etl\Models\Interfaces;

use App\DwhControl\Common\Models\Interfaces\ModelInterface;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Matchory\Elasticsearch\Query;

interface EtlDefinitionInterface extends ModelInterface
{

    /**
     * @param CarbonInterface $from
     * @param CarbonInterface $to
     * @param string $field
     * @param int $limit
     * @return Collection<EtlExecutionInterface>
     */
    public function getExecutions(CarbonInterface $from, CarbonInterface $to, $field = null, $limit = 28): Collection;

    /**
     * @param CarbonInterface $from
     * @param CarbonInterface $to
     * @param null $field
     * @param int $limit
     * @return Collection
     */
    public function getSuccessfulExecutions(CarbonInterface $from, CarbonInterface $to, $field = null, $limit = 28): Collection;

    /**
     * @param Query $query
     * @return Query
     */
    public function scopeSuccessful(Query $query): Query;

    /**
     * @return HasOne
     */
    public function statistic(): HasOne;

}
