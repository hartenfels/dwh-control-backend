<?php

namespace App\EtlMonitor\Api\Traits;

use App\EtlMonitor\Api\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

trait UsesDefaultStoreMethodTrait
{

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function store(Request $request, int $id): JsonResponse
    {
        return $this->_store($request, $id);
    }

}
