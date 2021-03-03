<?php

namespace App\EtlMonitor\Api\Http\Transformers;

use App\EtlMonitor\Common\Models\Model;

abstract class Transformer implements TransformerInterface
{

    public function __invoke(Model $model): array
    {
        return $this->transform($model);
    }

    abstract public function transform(Model $model): array;

}
