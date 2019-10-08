<?php

namespace Fikrimi\Pipe\Repositories;

use Fikrimi\Pipe\Interfaces\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    protected static $modelName;
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $name;
    protected $columns;

    public function __construct()
    {
        $this->setModel(new static::$modelName);

        $this->columns = \Schema::getColumnListing($this->model->getTable());
    }

    public function __isset($name)
    {
        return $this->__get($name) ? true : false;
    }

    public function __get($name)
    {
        return $this->model->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->model->$name = $value;
    }

    public function fill($attr)
    {
        $this->model->fill($attr);

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    public function setModel(\Illuminate\Database\Eloquent\Model $model)
    {
        $this->model = $model;

        return $this;
    }

    public function store(array $array = [])
    {
        $this->model->save();

        return $this->model;
    }
}
