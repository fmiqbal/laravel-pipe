<?php

namespace Fikrimi\Pipe\Models;

use Fikrimi\Pipe\Models\Traits\HasCreator;
use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    use HasCreator;

    public const T_SSH = 0;
    public const T_PASS = 1;
    public static $typeNames = [
        self::T_SSH  => 'SSH',
        self::T_PASS => 'Password',
    ];

    protected $guarded = [];
}
