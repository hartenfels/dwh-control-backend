<?php

namespace App\EtlMonitor\Sla\Http\Controllers;

use App\EtlMonitor\Api\Attributes\CustomAction;
use App\EtlMonitor\Api\Http\Controllers\Actions\Action;
use App\EtlMonitor\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultIndexMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultShowMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultStoreMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\EtlMonitor\Sla\Models\Sla;
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

        $query = Sla::query()->inRange($start, $end);

        if ($this->request->has('all')) {
            return $this->respondWithModels($query->get());
        }

        return $this->respondFilteredAndPaginated($query);
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
