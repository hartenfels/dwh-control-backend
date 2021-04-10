<?php

namespace App\DwhControl\Api\Traits;

use App\DwhControl\Api\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

trait UsesDefaultUpdateMethodTrait
{

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($request, $id);
    }

}
