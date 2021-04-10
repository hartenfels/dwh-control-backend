<?php

namespace App\DwhControl\Common\Http\Controllers;

use App\DwhControl\Api\Attributes\CustomAction;
use App\DwhControl\Api\Http\Controllers\Actions\Action;
use App\DwhControl\Api\Traits\UsesDefaultIndexMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultShowMethodTrait;
use App\DwhControl\Common\Exceptions\ModelNotFoundException;
use App\DwhControl\Common\ModelFinderService;
use App\DwhControl\Common\Http\Requests\AcknowledgeAlertRequest;
use App\DwhControl\Common\Models\Alert;
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
