<?php

namespace Fikrimi\Pipe\Models;

class Build extends BaseModel
{
    public const S_PROVISIONING = 0;
    public const S_RUNNING = 1;
    public const S_SUCCESS = 2;
    public const S_FAILED = 3;
    public const S_TERMINATED = 4;

    public static $statusNames = [
        self::S_PROVISIONING => 'provisioning',
        self::S_RUNNING      => 'running',
        self::S_SUCCESS      => 'success',
        self::S_FAILED       => 'failed',
        self::S_TERMINATED   => 'terminated',
    ];
    public $incrementing = false;
    protected $casts = [
        'status'       => 'int',
        'meta'         => 'json',
        'meta_steps'   => 'json',
        'meta_project' => 'json',
    ];
    protected $guarded = [];

    public static function getFinishStatuses()
    {
        return [
            self::S_FAILED, self::S_TERMINATED, self::S_SUCCESS,
        ];
    }

    public function getCacheKey()
    {
        return $this->id;
    }

    //public function getMetaProjectObjAttribute()
    //{
    //    return json_encode(j)
    //}
    //
    public function getStatusNameAttribute()
    {
        return self::$statusNames[$this->status];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
