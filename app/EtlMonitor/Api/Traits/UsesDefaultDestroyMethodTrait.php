<?php

namespace App\EtlMonitor\Api\Traits;

use App\EtlMonitor\Api\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

trait UsesDefaultDestroyMethodTrait
{

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        return $this->_destroy($request, $id);
    }

}
