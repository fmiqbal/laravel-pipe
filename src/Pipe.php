<?php

namespace Fikrimi\Pipe;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class Pipe
{
    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        Route::group($options, function (Router $router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }
}
