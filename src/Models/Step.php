<?php

namespace Fikrimi\Pipe\Models;

class Step extends BaseModel
{
    protected $guarded = [];

    public function build()
    {
        return $this->belongsTo(Build::class);
    }
}
