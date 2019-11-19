<?php

namespace Fikrimi\Pipe\Models;

/**
 * Fikrimi\Pipe\Models\Stack
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property array $commands
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Stack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Stack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Stack query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Stack whereCommands($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Stack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Stack whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Stack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Stack whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Stack whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
