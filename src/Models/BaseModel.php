<?php

namespace Fikrimi\Pipe\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Fikrimi\Pipe\Models\BaseModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\BaseModel query()
 * @mixin \Eloquent
 */
class BaseModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'pipe_' . $this->getTable();
    }
}
