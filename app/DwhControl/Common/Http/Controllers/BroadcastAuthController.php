<?php

namespace App\DwhControl\Common\Http\Controllers;


use App\DwhControl\Common\Http\Requests\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Broadcast;

class BroadcastAuthController extends Controller
{

    public function index(): JsonResponse
    {
        // TODO: Implement index() method.
    }

    public function show(int $id): JsonResponse
    {
        // TODO: Implement show() method.
    }

    public function authenticate(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->reflash();
        }

        return Broadcast::auth($request);
    }
}
