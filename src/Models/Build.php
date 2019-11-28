<?php

namespace Fikrimi\Pipe\Models;

use Carbon\Carbon;
use Fikrimi\Pipe\Jobs\BuildProject;
use Fikrimi\Pipe\Jobs\SwitchBuild;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
 * @method static Builder|Build newModelQuery()
 * @method static Builder|Build newQuery()
 * @method static Builder|Build query()
 * @method static Builder|Build whereBranch($value)
 * @method static Builder|Build whereCreatedAt($value)
 * @method static Builder|Build whereErrors($value)
 * @method static Builder|Build whereId($value)
 * @method static Builder|Build whereInvoker($value)
 * @method static Builder|Build whereMetaProject($value)
 * @method static Builder|Build whereProjectId($value)
 * @method static Builder|Build whereStartedAt($value)
 * @method static Builder|Build whereStatus($value)
 * @method static Builder|Build whereStoppedAt($value)
 * @method static Builder|Build whereUpdatedAt($value)
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
    public const I_MANUAL = 'manual';
    public const I_WEBHOOK = 'webhook';

    public static $statusNames = [
        self::S_PROVISIONING => 'provisioning',
        self::S_RUNNING      => 'running',
        self::S_SUCCESS      => 'success',
        self::S_FAILED       => 'failed',
        self::S_TERMINATED   => 'terminated',
        self::S_PENDING_TERM => 'pending termination',
    ];
    public static $invokers = [
        self::I_MANUAL, self::I_WEBHOOK,
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

    public static function switchTo(Build $build)
    {
        SwitchBuild::dispatch($build);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->forceFill([
                'id'           => Str::orderedUuid()->toString(),
                'status'       => Build::S_PROVISIONING,
                'meta_project' => Project::find($model->project_id)->toArray(),
            ]);
        });

        static::created(function (Model $model) {
            BuildProject::dispatch($model);
        });
    }

    public function getDurationAttribute()
    {
        if ($this->stopped_at) {
            return $this->started_at->diff($this->stopped_at);
        }

        return Carbon::now()->diff(Carbon::now());
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
        if (
            $this->status === self::S_RUNNING
            && $this->started_at !== null
            && Carbon::now() > $this->started_at->addSecond($this->meta_project['timeout'])
        ) {
            $this->update([
                'status'     => self::S_FAILED,
                'stopped_at' => $this->started_at->addSeconds($this->project->timeout),
            ]);
        }
    }
}
