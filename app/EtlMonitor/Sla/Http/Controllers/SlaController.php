<?php

namespace App\EtlMonitor\Sla\Http\Controllers;

use App\EtlMonitor\Api\Attributes\CustomAction;
use App\EtlMonitor\Api\Http\Controllers\Actions\Action;
use App\EtlMonitor\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultIndexMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultShowMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultStoreMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\EtlMonitor\Sla\Services\SlaDefinitionRetrievalService;
use App\EtlMonitor\Sla\Services\SlaRetrievalService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SlaController extends Controller
{
    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultStoreMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

    /**
     * @param string $start
     * @param string $end
     * @return JsonResponse
     * @throws AuthorizationException
     */
    #[CustomAction(Action::INDEX)]
    public function inRange(string $start, string $end): JsonResponse
    {
        $this->auth();

        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        $query = DB::table('etlmonitor_sla__slas')->where(function (Builder $builder) use ($start, $end) {
            return $builder->where(function (Builder $b) use ($start) {
                return $b->where('range_start', '<=', $start)
                    ->where('range_end', '>=', $start);
            })->orWhere(function (Builder $b) use ($end) {
                return $b->where('range_start', '<=', $end)
                    ->where('range_end', '>=', $end);
            });
        });

        $model_retrieval_service = function ($result) {
            return SlaRetrievalService::make($result->type, $result->id)->invoke();
        };

        if ($this->request->has('all')) {
            return $this->respondWithModels($query->get()->map($model_retrieval_service));
        }

        return $this->respondFilteredAndPaginated($query, $model_retrieval_service);
    }

    /**
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function _index(): JsonResponse
    {
        $this->auth();
    }

}
