<?php

namespace Fikrimi\Pipe\Stacks;

class Laravel extends BaseStack
{
    public $name = 'Laravel';
    public $description = 'For Laravel';
    public $commands = [
        'npm install',
        'npm run prod',
        'composer install',
        'php artisan migrate --force',
        'php artisan db:seed --force',
        'php artisan config:cache',
    ];
}