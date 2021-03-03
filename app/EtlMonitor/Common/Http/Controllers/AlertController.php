<?php

namespace App\EtlMonitor\Common\Http\Controllers;

use App\EtlMonitor\Api\Attributes\CustomAction;
use App\EtlMonitor\Api\Http\Controllers\Actions\Action;
use App\EtlMonitor\Api\Traits\UsesDefaultIndexMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultShowMethodTrait;
use App\EtlMonitor\Common\Exceptions\ModelNotFoundException;
use App\EtlMonitor\Common\ModelFinderService;
use App\EtlMonitor\Common\Http\Requests\AcknowledgeAlertRequest;
use App\EtlMonitor\Common\Models\Alert;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class AlertController extends Controller
{

    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait;

    /**
     * @param AcknowledgeAlertRequest $request
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws AuthorizationException
     */
    #[CustomAction(Action::UPDATE)]
    public function acknowledge(AcknowledgeAlertRequest $request): JsonResponse
    {
        $this->auth();

        $alert = ModelFinderService::findOrFail(Alert::class, $request->valid()->id);
        $alert->ack();

        return $this->respondWithModel($alert);
    }

}
