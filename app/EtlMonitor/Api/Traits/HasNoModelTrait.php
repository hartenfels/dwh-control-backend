<?php

namespace App\EtlMonitor\Api\Traits;

use App\EtlMonitor\Api\Http\Requests\Request;
use App\EtlMonitor\Common\Enum\HttpStatusCodeEnum;
use Illuminate\Http\JsonResponse;

trait HasNoModelTrait
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return $this->respondWithError('Method "create" not available for this endpoint', HttpStatusCodeEnum::Not_Implemented());
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->respondWithError('Method "index" not available for this endpoint', HttpStatusCodeEnum::Not_Implemented());
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        return $this->respondWithError('Method "show" not available for this endpoint', HttpStatusCodeEnum::Not_Implemented());
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->respondWithError('Method "update" not available for this endpoint', HttpStatusCodeEnum::Not_Implemented());
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        return $this->respondWithError('Method "delete" not available for this endpoint', HttpStatusCodeEnum::Not_Implemented());
    }

}
