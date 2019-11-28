<?php

namespace Fikrimi\Pipe\Models;
use Illuminate\Foundation\Auth\User;

interface UserOwnable
{
    public function setAutoCreator($status);

    /**
     * @param \Illuminate\Foundation\Auth\User|null $user
     * @return \Fikrimi\Pipe\Models\BaseModel
     */
    public function setCreator(User $user = null);

    public function creator();

    public function getCreatorColumn();

    public function getCreatorPrimaryKey();
}
