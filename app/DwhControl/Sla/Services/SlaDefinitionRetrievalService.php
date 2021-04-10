<?php

namespace App\DwhControl\Sla\Services;

use App\DwhControl\Common\Services\Service;
use App\DwhControl\Sla\Exceptions\InvalidSlaDefinitionTypeException;
use App\DwhControl\Sla\Models\AvailabilitySlaDefinition;
use App\DwhControl\Sla\Models\DeliverableSlaDefinition;
use App\DwhControl\Sla\Models\Interfaces\SlaDefinitionInterface;

class SlaDefinitionRetrievalService extends Service
{

    /**
     * @var string[]
     */
    private $valid_types = [
        'deliverable' => DeliverableSlaDefinition::class,
        'availability' => AvailabilitySlaDefinition::class
    ];

    /**
     * SlaDefinitionRetrievalService constructor.
     * @param string $type
     * @param int $id
     */
    public function __construct(private string $type, private int $id)
    {}

    /**
     * @return SlaDefinitionInterface
     * @throws InvalidSlaDefinitionTypeException
     */
    public function __invoke(): SlaDefinitionInterface
    {
        if (!in_array($this->type, array_keys($this->valid_types))) {
            throw new InvalidSlaDefinitionTypeException($this->type);
        }

        return $this->valid_types[$this->type]::find($this->id);
    }

}
