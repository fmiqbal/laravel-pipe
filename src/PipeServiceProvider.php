<?php

namespace Fikrimi\Pipe;

use App;
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
            __DIR__ . '/config.php' => config_path() . '/pipe.php',
            __DIR__ . '/Views/assets' => public_path('pipe-assets'),
        ], 'pipe');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $repositories = [
            'CredentialRepo' => \Fikrimi\Pipe\Repositories\CredentialRepo::class,
        ];

        foreach ($repositories as $name => $class) {
            App::bind($name, function () use ($class) {
                return new $class();
            });
        }
    }
}
