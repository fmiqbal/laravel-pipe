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

        $defaultOptions = [
            'prefix'    => 'pipe',
            'namespace' => '\Fikrimi\Pipe\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function (Router $router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }
}
