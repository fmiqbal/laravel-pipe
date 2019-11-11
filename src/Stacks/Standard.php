<?php

namespace Fikrimi\Pipe\Stacks;

class Standard extends BaseStack
{
    public $name = 'Standard';
    public $description = 'Nothing executed, just build and deploy';
    public $commands = [
        'npm install',
        'npm run build',
    ];
}