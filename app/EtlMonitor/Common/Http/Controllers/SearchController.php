<?php

namespace App\EtlMonitor\Common\Http\Controllers;

use App\EtlMonitor\Api\Attributes\CustomAction;
use App\EtlMonitor\Api\Http\Controllers\Actions\Action;
use App\EtlMonitor\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultIndexMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultShowMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultStoreMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\EtlMonitor\Common\Transfer\AutocompleteResult;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{

    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultStoreMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

    /**
     * @param string $search_text
     * @return JsonResponse
     */
    #[CustomAction(Action::INDEX)]
    public function autocomplete(string $search_text): JsonResponse
    {
        $results = [];

        collect(config('etl_monitor.searchable_models'))->each(function(string $static) use ($search_text, &$results) {
            $static::autocomplete($search_text)->each(function (AutocompleteResult $result) use (&$results) {
                if (!isset($results[$result->entity])) $results[$result->entity] = [];
                $results[$result->entity][] = $result;
            });
        });

        return $this->respondWithData($results);
    }

}
