<?php

namespace Fikrimi\Pipe\Facades\Repositories;

class CredentialRepo extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return new \Fikrimi\Pipe\Repositories\CredentialRepo();
    }
}
