<?php

namespace Fikrimi\Pipe;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class Pipe
{
    public static function routes($prefix = 'pipe', $callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) use ($prefix) {
            $router->all($prefix);
        };

        Route::group($options, function (Router $router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }
}
