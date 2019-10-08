<?php

namespace Fikrimi\Pipe\Interfaces;

use Illuminate\Http\Request;

interface RepositoryInterface
{
    public function fromRequest(Request $request);

    public function fill($attr);

    public function fromArray($array);

    public function store(array $array = []);
}
