<?php

namespace Fikrimi\Pipe\Http\Controllers\Traits;

use Illuminate\Contracts\Auth\Access\Gate;

trait HasPolicy
{
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

    public function checkModelCreator($action, \Illuminate\Database\Eloquent\Builder $model)
    {
        if (
            config('pipe.modules.auth')
            && ! config('pipe.auth.policies.credentials.' . $action)
        ) {
            $model
                ->where('created_by', auth()->id())
                ->with('creator');

            return $model;
        }

        return true;
    }
}
