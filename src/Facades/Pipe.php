<?php

namespace Fikrimi\Pipe\Facades;

use Illuminate\Support\Facades\Facade;

class Pipe extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pipe';
    }
}
