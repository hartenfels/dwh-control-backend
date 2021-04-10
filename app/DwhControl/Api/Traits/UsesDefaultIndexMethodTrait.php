<?php

namespace App\DwhControl\Api\Traits;

use Illuminate\Http\JsonResponse;

trait UsesDefaultIndexMethodTrait
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->_index();
    }

}
