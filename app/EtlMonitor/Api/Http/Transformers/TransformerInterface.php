<?php

namespace App\EtlMonitor\Api\Http\Transformers;

use App\EtlMonitor\Common\Models\Model;

interface TransformerInterface
{

    public function transform(Model $model): array;

}
