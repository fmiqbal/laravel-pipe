<?php

namespace Fikrimi\Pipe\Enum;

class Provider extends Enum
{
    public const GITLAB = 1;
    public const GITHUB = 2;
    public const BITBUCKET = 3;

    public static $names = [
        self::GITLAB    => 'GitLab',
        self::GITHUB    => 'GitHub',
        self::BITBUCKET => 'BitBucket',
    ];
}
