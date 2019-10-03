<?php

namespace Fikrimi\Pipe\Models;

use Fikrimi\Pipe\Models\Traits\HasCreator;

class Credential extends BaseModel
{
    use HasCreator;

    public const T_KEY = 0;
    public const T_PASS = 1;
    public static $typeNames = [
        self::T_KEY  => 'Private Key',
        self::T_PASS => 'Password',
    ];

    public static $typeAuth = [
        self::T_KEY  => 'keytext',
        self::T_PASS => 'password',
    ];

    protected $guarded = [];

    public static function getAuth($credential)
    {
        return self::$typeAuth[$credential['type']];
    }
}
