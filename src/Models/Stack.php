<?php

namespace Fikrimi\Pipe\Models;

class Stack extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'commands'
    ];

    protected $casts = [
        'commands' => 'json'
    ];
}
