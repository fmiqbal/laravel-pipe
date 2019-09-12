<?php

namespace Fikrimi\Pipe\Models;

use Illuminate\Database\Eloquent\Model;

class Build extends Model
{
    protected $guarded = [];
    public $incrementing = false;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
