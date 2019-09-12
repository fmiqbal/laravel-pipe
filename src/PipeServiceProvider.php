<?php

namespace Fikrimi\Pipe;

use Illuminate\Support\ServiceProvider;

class PipeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'pipe');
        $this->loadMigrationsFrom(__DIR__ . '/Databases/migrations');
        $this->publishes([
            __DIR__ . '/Views/assets' => public_path('pipe-assets')
        ], 'pipe-assets');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
