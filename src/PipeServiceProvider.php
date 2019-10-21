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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pipe');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->publishes([
            __DIR__ . '/../config/pipe.php'        => config_path() . '/pipe.php',
            __DIR__ . '/../resources/views/assets' => public_path('pipe-assets'),
        ], 'pipe');
        $this->mergeConfigFrom(__DIR__ . '/../config/pipe.php', 'pipe');
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
