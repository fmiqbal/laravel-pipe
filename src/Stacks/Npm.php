<?php

namespace Fikrimi\Pipe\Stacks;

class Npm extends BaseStack
{
    public $name = 'NPM';
    public $description = 'For npm standard';
    public $commands = [
        'npm install',
        'npm run build',
    ];
}