<?php

namespace Fikrimi\Pipe\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'pipe_' . $this->getTable();
    }
}
