<?php

namespace Fikrimi\Pipe\Models;

class Stack extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'commands' => 'json'
    ];
}
