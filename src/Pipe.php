<?php

namespace Fikrimi\Pipe;

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
            'namespace' => '\Fikrimi\Pipe',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }
}
