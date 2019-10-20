<?php

namespace Fikrimi\Pipe\Tests;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var \Illuminate\Foundation\Auth\User
     */
    protected $user;

    public function checkAuth($route, $method = 'get', $payload = [])
    {
        $this->withExceptionHandling();

        /** @var $response \Illuminate\Foundation\Testing\TestResponse */
        $response = $this->$method($route, $payload);

        $response->assertStatus(302);

        $this->actingAs($this->user);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Fikrimi\Pipe\PipeServiceProvider::class,
            \Fikrimi\Pipe\AuthServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->withFactories(__DIR__ . '/../database/factories');

        $this->user = factory(\Illuminate\Foundation\Auth\User::class)->create();

        Route::group([
            'middleware' => ['auth', 'web']
        ], function () {
            \Illuminate\Support\Facades\Auth::routes();
            \Fikrimi\Pipe\Pipe::routes('');
        });

        $this->withoutExceptionHandling();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('pipe.modules.auth', true);
    }
}
