<?php

namespace App\EtlMonitor\Api\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureAuthenticated
{

    public function handle($request, Closure $next)
    {
        if (!Auth::user()) {
            abort(401, 'Unauthenticated');
        }

        return $next($request);
    }

}
