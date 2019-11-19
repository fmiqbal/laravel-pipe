<?php

namespace Fikrimi\Pipe\Models;

use Fikrimi\Pipe\Jobs\BuildProject;
use Illuminate\Support\Str;

/**
 * Fikrimi\Pipe\Models\Build
 *
 * @property string $id
 * @property string $project_id
 * @property string $invoker
 * @property string $branch
 * @property int $status
 * @property string|null $errors
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $stopped_at
 * @property array $meta_project
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $duration
 * @property-read mixed $status_name
 * @property-read \Fikrimi\Pipe\Models\Project $project
 * @property-read \Illuminate\Database\Eloquent\Collection|\Fikrimi\Pipe\Models\Step[] $steps
 * @property-read int|null $steps_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereErrors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereInvoker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereMetaProject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Build whereStoppedAt($value)
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
    protected $fillable = [
        'status',
        'branch',
        'started_at',
        'stopped_at',
    ];
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function (\Illuminate\Database\Eloquent\Model $model) {
            $model->forceFill([
                'id'           => Str::orderedUuid()->toString(),
                'status'       => Build::S_PROVISIONING,
                'meta_project' => Project::find($model->project_id)->toArray(),
            ]);
        });

        static::created(function (\Illuminate\Database\Eloquent\Model $model) {
            BuildProject::dispatch($model);
        });
    }

    public function getDurationAttribute()
    {
        if ($this->stopped_at) {
            return $this->started_at->diff($this->stopped_at);
        }

        return '-1';
    }

    public function getCacheKey($for)
    {
        return "pipe-cache-build-$for-{$this->id}";
    }

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
        if ($this->status === self::S_RUNNING && \Carbon\Carbon::now() > $this->started_at && $this->started_at !== null) {
            $this->update([
                'status'     => self::S_FAILED,
                'stopped_at' => $this->started_at->addSeconds($this->project->timeout),
            ]);
        }
    }
}
