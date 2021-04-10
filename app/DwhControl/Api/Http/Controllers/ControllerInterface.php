<?php

namespace App\DwhControl\Api\Http\Controllers;

use App\DwhControl\Api\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

interface ControllerInterface
{

    public function __construct(Request $request);

    public function index(): JsonResponse;

    public function show(int $id): JsonResponse;

}
