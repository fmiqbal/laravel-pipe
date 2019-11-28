<?php

namespace Fikrimi\Pipe\Tests;

use Fikrimi\LaravelHelper\HelperServiceProvider;
use Fikrimi\Pipe\AuthServiceProvider;
use Fikrimi\Pipe\Models\UserOwnable;
use Fikrimi\Pipe\Pipe;
use Fikrimi\Pipe\PipeServiceProvider;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public const F_MAKE = 'make';
    public const F_CREATE = 'create';

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

    /**
     * @param $model
     * @param string $type
     * @param null $creator
     * @return \Fikrimi\Pipe\Models\BaseModel
     */
    public function createResource($model, $type = self::F_MAKE, $creator = null)
    {
        /** @var \Fikrimi\Pipe\Models\BaseModel $resource */
        $resource = factory($model)->$type();

        if ($creator !== null && $resource instanceof UserOwnable) {
            if ($creator instanceof User) {
                $resource->setCreator($creator)->save();
            }

            if ($creator === 'other') {
                $resource->setCreator(factory(User::class)->create())->save();
            }
        }

        return $resource;
    }

    protected function getPackageProviders($app)
    {
        return [
            PipeServiceProvider::class,
            AuthServiceProvider::class,
            HelperServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->withFactories(__DIR__ . '/../database/factories');

        $this->user = factory(User::class)->create();

        Route::group([
            'middleware' => ['auth', 'web'],
        ], function () {
            Auth::routes();
            Pipe::routes('');
        });

        $this->withoutExceptionHandling();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('pipe.modules.auth', true);
    }
}
