<?php

namespace App\DwhControl\Etl\Models;

use App\DwhControl\Etl\Models\Interfaces\EtlDefinitionInterface;
use App\DwhControl\Etl\Models\Interfaces\EtlExecutionInterface;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Matchory\Elasticsearch\Query;

class GenericEtlDefinition extends EtlDefinition
{

    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'generic'
    ];

    /**
     * @var string
     */
    protected static string $type = 'generic';

    /**
     * @param CarbonInterface $from
     * @param CarbonInterface $to
     * @param string|null $field
     * @param ?int $limit
     * @return Collection
     */
    public function getExecutions(CarbonInterface $from, CarbonInterface $to, ?string $field = 'date.end_pp', ?int $limit = 28): Collection
    {

    }

    /**
     * @param string|null $field
     * @return EtlExecutionInterface|null
     */
    public function getLatestExecution(?string $field = 'date.end_pp'): ?EtlExecutionInterface
    {

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

    }

    /**
     * @return EtlDefinitionInterface
     */
    public function updateFromExecution(): EtlDefinitionInterface
    {
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'mdi-atom';
    }

}
