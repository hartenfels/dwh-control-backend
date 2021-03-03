<?php

namespace App\EtlMonitor\Api\Traits;

use Illuminate\Http\JsonResponse;

trait UsesDefaultShowMethodTrait
{

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        return $this->_show($id);
    }

}
