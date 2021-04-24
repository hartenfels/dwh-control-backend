<?php

namespace App\DwhControl\Etl\Http\Controllers;

use App\DwhControl\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultIndexMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultShowMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultStoreMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultUpdateMethodTrait;

class EtlDefinitionController extends Controller
{
    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultStoreMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

}
