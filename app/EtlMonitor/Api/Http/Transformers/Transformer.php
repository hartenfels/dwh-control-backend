<?php

namespace App\EtlMonitor\Api\Http\Transformers;

use App\EtlMonitor\Common\Models\ElasticsearchModel;
use App\EtlMonitor\Common\Models\Model;

abstract class Transformer implements TransformerInterface
{

    public function __invoke(Model|ElasticsearchModel $model): array
    {
        return $this->transform($model);
    }

    abstract public function transform(Model|ElasticsearchModel $model): array;

}
