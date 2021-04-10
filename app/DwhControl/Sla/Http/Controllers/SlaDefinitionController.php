<?php

namespace App\DwhControl\Sla\Http\Controllers;

use App\DwhControl\Api\Attributes\CustomAction;
use App\DwhControl\Api\Http\Controllers\Actions\Action;
use App\DwhControl\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultIndexMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultShowMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultStoreMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\DwhControl\Sla\Models\SlaDefinition;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class SlaDefinitionController extends Controller
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

        $query = SlaDefinition::query()->with(['slas' => function ($q) use ($start, $end) {
            $q->inRange($start, $end);
        }]);

        if ($this->request->has('all')) {
            return $this->respondFiltered($query);
        }

        return $this->respondFilteredAndPaginated($query);
    }

}
