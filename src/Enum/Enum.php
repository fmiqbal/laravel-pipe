<?php

namespace Fikrimi\Pipe\Enum;

abstract class Enum
{
    public static function all()
    {
        return static::getReflection()->getConstants();
    }

    private static function getReflection()
    {
        return new \ReflectionClass(static::class);
    }
}
