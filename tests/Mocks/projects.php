<?php

use Fikrimi\Pipe\Enum\Repository;
use Fikrimi\Pipe\Models\Credential;
use Fikrimi\Pipe\Models\Stack;
use Illuminate\Support\Str;

return [
    'repository'    => Repository::GITLAB,
    'name'          => 'HTML Local',
    'namespace'     => 'pipe-tester/html',
    'dir_deploy'    => '/srv/http/test_pipe/deploy/backend',
    'dir_workspace' => '/srv/http/test_pipe/workspace',
    'host'          => '127.0.0.1',
    'branch'        => 'master',
    'commands'      => Stack::where('name', 'Standard')->first()->commands,
    'id'            => Str::orderedUuid(),
    'credentials'   => [
        'username' => 'fikrimi',
        'type'     => Credential::T_PASS,
        'auth'     => 'Q7NC7nap',
    ],
];
