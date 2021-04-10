<?php

namespace App\DwhControl\Common\Enum;

/**
 * @method static DatabaseDataTypesEnum DATATYPE_STRING
 * @method static DatabaseDataTypesEnum DATATYPE_BOOL
 * @method static DatabaseDataTypesEnum DATATYPE_BIGINT
 * @method static DatabaseDataTypesEnum DATATYPE_FLOAT
 * @method static DatabaseDataTypesEnum DATATYPE_JSON_ARRAY
 * @method static DatabaseDataTypesEnum DATATYPE_JSON_OBJECT
 */
class DatabaseDataTypesEnum extends Enum
{

    private const DATATYPE_STRING = 'string';
    private const DATATYPE_BOOL = 'bool';
    private const DATATYPE_BIGINT = 'bigint';
    private const DATATYPE_FLOAT = 'float';
    private const DATATYPE_JSON_ARRAY = 'json_array';
    private const DATATYPE_JSON_OBJECT = 'json_object';

}
