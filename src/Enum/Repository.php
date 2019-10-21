<?php

namespace Fikrimi\Pipe\Enum;

class Repository extends Enum
{
    public const GITLAB = 1;
    public const GITHUB = 2;
    public const BITBUCKET = 3;

    public static $names = [
        self::GITLAB    => 'GitLab',
        self::GITHUB    => 'GitHub',
        self::BITBUCKET => 'BitBucket',
    ];

    public static $repositoryUrlSsh = [
        self::GITLAB    => 'git@gitlab.com:',
        self::GITHUB    => 'git@github.com:',
        self::BITBUCKET => 'git@bitbucket.com:',
    ];
}
