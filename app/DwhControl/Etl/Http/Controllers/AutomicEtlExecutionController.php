<?php

namespace App\DwhControl\Etl\Http\Controllers;

use App\DwhControl\Api\Attributes\CustomAction;
use App\DwhControl\Api\Http\Controllers\Actions\Action;
use App\DwhControl\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultIndexMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultShowMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultStoreMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\DwhControl\Etl\Models\AutomicEtlDefinition;
use App\DwhControl\Etl\Models\AutomicEtlExecution;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AutomicEtlExecutionController extends Controller
{
    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultStoreMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

    #[CustomAction(Action::INDEX)]
    public function inRange(string $start, string $end): JsonResponse
    {
        $this->auth();

        $start = Carbon::parse($start)->format('c');
        $end = Carbon::parse($end)->format('c');

        $query = [
            'query' => [
                'bool' => [
                    'minimum_should_match' => 1,
                    'must' => [],
                    'should' => [
                        [
                            'range' => [
                                'date.activation' => [
                                    'gte' => $start,
                                    'lte' => $end
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'date.start' => [
                                    'gte' => $start,
                                    'lte' => $end
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'date.end' => [
                                    'gte' => $start,
                                    'lte' => $end
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'date.end_pp' => [
                                    'gte' => $start,
                                    'lte' => $end
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if ($this->request->has('filter') && isset($this->request->get('filter')['etl_id'])) {
            $query['query']['bool']['must'][] = [
                'match' => [
                    'etl_id.keyword' => $this->request->get('filter')['etl_id']
                ]
            ];
        }

        $models = AutomicEtlExecution::query()->body($query)->take(config('dwh_control.etl_executions_elasticsearch_maxtake'))->get();

        return $this->respondWithModels(collect($models));
    }

}
