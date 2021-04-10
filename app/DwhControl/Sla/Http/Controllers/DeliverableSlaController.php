<?php

namespace App\DwhControl\Sla\Http\Controllers;

use App\DwhControl\Api\Attributes\CustomAction;
use App\DwhControl\Api\Http\Controllers\Actions\Action;
use App\DwhControl\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultIndexMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultShowMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultStoreMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\DwhControl\Sla\Models\DeliverableSla;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class DeliverableSlaController extends Controller
{
    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultStoreMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

}
