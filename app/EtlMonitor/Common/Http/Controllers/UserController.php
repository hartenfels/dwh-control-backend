<?php

namespace App\EtlMonitor\Common\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{


    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(int $id): JsonResponse
    {
        if (Auth::user()->id != $id) {
            return $this->respondUnauthorized();
        }

        return $this->_show($id);
    }

    public function index(): JsonResponse
    {
        // TODO: Implement index() method.
    }
}
