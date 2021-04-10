<?php

namespace App\DwhControl\Common\Http\Controllers;

use App\DwhControl\Api\Attributes\CustomAction;
use App\DwhControl\Api\Http\Controllers\Actions\Action;
use App\DwhControl\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultIndexMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultShowMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultStoreMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\DwhControl\Common\Transfer\AutocompleteResult;
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

        collect(config('dwh_control.searchable_models'))->each(function(string $static) use ($search_text, &$results) {
            $static::autocomplete($search_text)->each(function (AutocompleteResult $result) use (&$results) {
                if (!isset($results[$result->entity])) $results[$result->entity] = [];
                $results[$result->entity][] = $result;
            });
        });

        return $this->respondWithData($results);
    }

}
