<?php

namespace App\DwhControl\Sla\Http\Controllers;

use App\DwhControl\Sla\Models\Sla\SlaInterface;
use App\DwhControl\Sla\Services\SlaDefinitionRetrievalService;
use App\DwhControl\Sla\Services\SlaRetrievalService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use SlaProgressIngestRequest;

class IngestController extends Controller
{

    /**
     * @param SlaProgressIngestRequest $request
     */
    public function progress(SlaProgressIngestRequest $request)
    {
        $time = $request->get('time') ? Carbon::parse($request->get('time')) : Carbon::now();
        $definition = SlaDefinitionRetrievalService::make($request->get('type'), $request->get('id'))->invoke();
        $slas = SlaRetrievalService::make($definition, $time)->invoke();

        $slas->each(function (SlaInterface $sla) use ($time, $request) {
            $sla->addProgress(
                $time, $request->get('progress_percent'), $request->get('source')
            );
        });
    }

    public function index(): JsonResponse
    {
        // TODO: Implement index() method.
    }

    public function show(int $id): JsonResponse
    {
        // TODO: Implement show() method.
    }
}
