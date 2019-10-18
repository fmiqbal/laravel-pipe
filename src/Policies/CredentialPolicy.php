<?php

namespace Fikrimi\Pipe\Policies;

use App\User;
use Fikrimi\Pipe\Models\Credential;
use Illuminate\Auth\Access\HandlesAuthorization;

class CredentialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the credential.
     *
     * @param  \App\User  $user
     * @param  \Fikrimi\Pipe\Models\Credential  $credential
     * @return mixed
     */
    public function view(User $user, Credential $credential)
    {
        if (config('pipe.auth.policies.credentials.view_other')) {
            return true;
        }

        return $user->{config('pipe.auth.primary_key')} === $credential->created_by;
    }

    /**
     * Determine whether the user can delete the credential.
     *
     * @param  \App\User  $user
     * @param  \Fikrimi\Pipe\Models\Credential  $credential
     * @return mixed
     */
    public function delete(User $user, Credential $credential)
    {
        if (config('pipe.auth.policies.credentials.delete_other')) {
            return true;
        }

        return $user->{config('pipe.auth.primary_key')} === $credential->created_by;
    }
}
