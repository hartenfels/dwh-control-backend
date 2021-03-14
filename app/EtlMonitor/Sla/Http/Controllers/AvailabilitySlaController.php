<?php

namespace App\EtlMonitor\Sla\Http\Controllers;

use App\EtlMonitor\Api\Attributes\CustomAction;
use App\EtlMonitor\Api\Http\Controllers\Actions\Action;
use App\EtlMonitor\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultIndexMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultShowMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultStoreMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\EtlMonitor\Sla\Models\DeliverableSla;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class AvailabilitySlaController extends Controller
{
    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultStoreMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

}
