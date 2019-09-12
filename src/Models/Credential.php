<?php

namespace Fikrimi\Pipe\Models;

use Fikrimi\Pipe\Enum\Provider;
use Fikrimi\Pipe\Models\Traits\HasCreator;
use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    use HasCreator;

    public const T_KEY = 0;
    public const T_PASS = 1;
    public static $typeNames = [
        self::T_KEY  => 'Private Key',
        self::T_PASS => 'Password',
    ];

    protected $guarded = [];

    public function getNameAttribute()
    {
        return $this->username . '@' . Provider::$names[$this->provider];
    }
}