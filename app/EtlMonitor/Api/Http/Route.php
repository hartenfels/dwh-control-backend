<?php

namespace App\EtlMonitor\Api\Http;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\Route as BaseRoute;
use Illuminate\Routing\RouteRegistrar;

class Route
{

    /**
     * @param array|string|null $middleware
     * @return RouteRegistrar
     */
    public static function middleware(array|string|null $middleware): RouteRegistrar
    {
        return \Illuminate\Support\Facades\Route::middleware($middleware);
    }

    /**
     * We are using a generic controller for most of our API requests.
     * Therefore we need to circumvent named parameters like /api/v1/users/{user}
     * and rename the parameter top /api/v1/users/{id}.
     *
     * @param string $name
     * @param string $controller
     * @return PendingResourceRegistration
     */
    public static function resource(string $name, string $controller): PendingResourceRegistration
    {
        return \Illuminate\Support\Facades\Route::resource($name, $controller, [
            'parameters' => [
                $name => 'id'
            ]
        ]);
    }

    /**
     * @param string $uri
     * @param array|string|callable|null $action
     * @return BaseRoute
     */
    public static function get(string $uri, array|string|callable|null $action = null): BaseRoute
    {
        return \Illuminate\Support\Facades\Route::get($uri, $action);
    }

    /**
     * @param string $uri
     * @param string $action
     * @return BaseRoute
     */
    public static function put(string $uri, string $action): BaseRoute
    {
        return \Illuminate\Support\Facades\Route::put($uri, $action);
    }

    /**
     * @param string $uri
     * @param array|string|callable|null $action
     * @return BaseRoute
     */
    public static function post(string $uri, array|string|callable|null $action = null): BaseRoute
    {
        return \Illuminate\Support\Facades\Route::post($uri, $action);
    }

}
