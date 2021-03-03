<?php

namespace App\EtlMonitor\Common\Traits;

use App\EtlMonitor\Common\Exceptions\MissingRequestFieldException;
use App\EtlMonitor\Common\Enum\DatabaseDataTypesEnum;

trait HasMultipleDataTypeFieldsTrait
{
    /**
     * @return mixed
     * @throws MissingRequestFieldException
     */
    public function getValueAttribute()
    {
        if (!in_array($this->datatype, DatabaseDataTypesEnum::values())) {
            throw new MissingRequestFieldException($this->datatype);
        }

        $column_name = 'value_' . $this->datatype;
        return $this->$column_name;
    }
}
