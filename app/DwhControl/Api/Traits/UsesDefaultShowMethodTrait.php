<?php

namespace App\DwhControl\Api\Traits;

use Illuminate\Http\JsonResponse;

trait UsesDefaultShowMethodTrait
{

    /**
     * @param int|string $id
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        return $this->_show($id);
    }

}
