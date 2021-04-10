<?php

namespace App\DwhControl\Api\Http\Transformers;

use App\DwhControl\Common\Models\ElasticsearchModel;
use App\DwhControl\Common\Models\Model;

abstract class Transformer implements TransformerInterface
{

    public function __invoke(Model|ElasticsearchModel $model): array
    {
        return $this->transform($model);
    }

    abstract public function transform(Model|ElasticsearchModel $model): array;

}
