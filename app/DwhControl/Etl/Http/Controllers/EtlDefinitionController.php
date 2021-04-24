<?php

namespace App\DwhControl\Etl\Http\Controllers;

use App\DwhControl\Api\Attributes\CustomAction;
use App\DwhControl\Api\Http\Controllers\Actions\Action;
use App\DwhControl\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultIndexMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultShowMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultStoreMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\DwhControl\Etl\Models\EtlDefinition;
use Illuminate\Http\JsonResponse;

class EtlDefinitionController extends Controller
{
    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultStoreMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[CustomAction(Action::INDEX)]
    public function depends_on(int $id): JsonResponse
    {
        $definitions = EtlDefinition::find($id)->depends_on()->wherePivot('etl_definition_id', $id);

        return $this->respondFilteredAndPaginated($definitions);
    }

}
