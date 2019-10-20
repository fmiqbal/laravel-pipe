<?php

namespace Fikrimi\Pipe\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // use RefreshDatabase;

    /**
     * @var \Illuminate\Foundation\Auth\User
     */
    protected $user;

    public function checkAuth($route, $method = 'get', $payload = [])
    {
        /** @var $response \Illuminate\Foundation\Testing\TestResponse */
        $response = $this->$method('/pipe' . $route, $payload);

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
            'middleware' => 'auth'
        ], function () {
            \Illuminate\Support\Facades\Auth::routes();
            \Fikrimi\Pipe\Pipe::routes();
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('pipe.modules.auth', true);
    }
}
