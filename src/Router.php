<?php

namespace Fikrimi\Pipe;

class Router extends \Illuminate\Routing\Router
{
    public function pipe()
    {
        $this->resource('pipe', PipeController::class);
    }
}
