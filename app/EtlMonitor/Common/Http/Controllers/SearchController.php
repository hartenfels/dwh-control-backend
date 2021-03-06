<?php

namespace App\EtlMonitor\Common\Http\Controllers;

use App\EtlMonitor\Api\Attributes\CustomAction;
use App\EtlMonitor\Api\Http\Controllers\Actions\Action;
use App\EtlMonitor\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultIndexMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultShowMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultStoreMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\EtlMonitor\Sla\Models\DeliverableSlaDefinition;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

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
        $sql_filter = '%' . $search_text . '%';
        $results = [];

        $sla_definition_collection = new Collection();

        /** @var Collection<DeliverableSlaDefinition> $definitions */
        $definitions = DeliverableSlaDefinition::where('name', 'like', $sql_filter)->limit(5)->get();
        $definitions->each(function (DeliverableSlaDefinition $d) use (&$sla_definition_collection) {
            $sla_definition_collection->add((object)[
                'id' => $d->id,
                'name' => $d->name,
                'info' => $d->statistic,
                'model' => $d->model(),
                'icon' => $d->getIcon(),
            ]);
        });
        $results['sla_definitions'] = $sla_definition_collection->toArray();

        return $this->respondWithData($results);
    }

}
