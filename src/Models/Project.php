<?php

namespace Fikrimi\Pipe\Models;

class Project extends BaseModel
{
    public $incrementing = false;
    protected $guarded = [];

    public function credential()
    {
        return $this->belongsTo(Credential::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function builds()
    {
        return $this->hasMany(Build::class);
    }
}
