<?php

namespace Fikrimi\Pipe\Http\Controllers;

use Illuminate\Contracts\Auth\Access\Gate;

trait HasPolicy
{
    private static $policyMap = [
        \Fikrimi\Pipe\Models\Project::class => 'projects',
        \Fikrimi\Pipe\Models\Credential::class => 'credentials',
    ];

    /** @noinspection PhpDocRedundantThrowsInspection */
    /**
     * Authorize a given action for the current user.
     *
     * @param mixed $ability
     * @param mixed|array $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize($ability, $arguments = [])
    {
        if (! config('pipe.modules.auth')) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            /** @noinspection PhpInconsistentReturnPointsInspection */
            return true;
        }

        [$ability, $arguments] = $this->parseAbilityAndArguments($ability, $arguments);

        return app(Gate::class)->authorize($ability, $arguments);
    }

    public function checkModelCreator($action, \Illuminate\Database\Eloquent\Builder $builder)
    {
        $policy = static::$policyMap[get_class($builder->getModel())];

        if (
            config('pipe.modules.auth')
            && ! config("pipe.auth.policies.{$policy}.$action")
        ) {
            $builder
                ->where('created_by', auth()->id())
                ->with('creator');

            return $builder;
        }

        return true;
    }
}
