<?php

namespace Fikrimi\Pipe;

class Stack
{
    public static function all()
    {
        return [
            new \Fikrimi\Pipe\Stacks\Npm(),
            new \Fikrimi\Pipe\Stacks\Laravel(),
            new \Fikrimi\Pipe\Stacks\Standard(),
        ];
    }
}