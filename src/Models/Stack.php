<?php

namespace Fikrimi\Pipe\Models;

class Stack extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'commands',
    ];

    protected $casts = [
        'commands' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (\Illuminate\Database\Eloquent\Model $model) {
            if (! is_array($model->commands)) {
                $model->commands = preg_split("/\s\s+/", $model->commands);
            }
        });
    }
}
