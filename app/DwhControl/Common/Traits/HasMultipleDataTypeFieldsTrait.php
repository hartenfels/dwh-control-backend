<?php

namespace App\DwhControl\Common\Traits;

use App\DwhControl\Common\Exceptions\MissingRequestFieldException;
use App\DwhControl\Common\Enum\DatabaseDataTypesEnum;

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
