<?php

namespace App\DwhControl\Api\Http\Transformers;

use App\DwhControl\Common\Models\Model;

interface TransformerInterface
{

    public function transform(Model $model): array;

}
