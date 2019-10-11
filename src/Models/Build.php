<?php

namespace Fikrimi\Pipe\Models;

/**
 * Fikrimi\Pipe\Models\Build
 *
 * @property string $id
 * @property string $project_id
 * @property string $invoker
 * @property int $status
 * @property array $meta
 * @property array $meta_steps
 * @property array $meta_project
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $status_name
 * @property-read \Fikrimi\Pipe\Models\Project $project
 * @property-read \Illuminate\Database\Eloquent\Collection|\Fikrimi\Pipe\Models\Step[] $steps
 * @property-read int|null $steps_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereInvoker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereMetaProject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereMetaSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Build extends BaseModel
{
    public const S_PROVISIONING = 0;
    public const S_RUNNING = 1;
    public const S_SUCCESS = 2;
    public const S_FAILED = 3;
    public const S_TERMINATED = 4;
    public const S_PENDING_TERM = 5;

    public static $statusNames = [
        self::S_PROVISIONING => 'provisioning',
        self::S_RUNNING      => 'running',
        self::S_SUCCESS      => 'success',
        self::S_FAILED       => 'failed',
        self::S_TERMINATED   => 'terminated',
        self::S_PENDING_TERM => 'pending termination',
    ];
    public $incrementing = false;
    protected $casts = [
        'status'       => 'int',
        'meta'         => 'json',
        'meta_steps'   => 'json',
        'meta_project' => 'json',
    ];
    protected $guarded = [];
    protected $dates = [
        'started_at',
        'stopped_at',
    ];

    public static function getFinishStatuses()
    {
        return [
            self::S_FAILED, self::S_TERMINATED, self::S_SUCCESS,
        ];
    }

    public function getCacheKey($for)
    {
        return "pipe-cache-build-$for-{$this->id}";
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

    public function steps()
    {
        return $this->hasMany(Step::class);
    }

    public function checkTimeOut()
    {
        if ($this->status === self::S_RUNNING && \Carbon\Carbon::now() > $this->started_at) {
            $this->update([
                'status' => self::S_FAILED,
                'stopped_at' => $this->started_at->addSeconds($this->project->timeout)
            ]);
        }
    }
}
