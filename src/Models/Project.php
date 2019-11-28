<?php

namespace Fikrimi\Pipe\Models;

use Fikrimi\Pipe\Exceptions\ApplicationException;
use Illuminate\Support\Str;

/**
 * Fikrimi\Pipe\Models\Project
 *
 * @property string $id
 * @property string $name
 * @property int $credential_id
 * @property int $repository
 * @property string $host
 * @property string $dir_deploy
 * @property string $dir_workspace
 * @property string $branch
 * @property int $keep_build
 * @property array $commands
 * @property int $timeout
 * @property string $namespace
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $current_build
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection|\Fikrimi\Pipe\Models\Build[] $builds
 * @property-read int|null $builds_count
 * @property-read \Illuminate\Foundation\Auth\User|null $creator
 * @property-read \Fikrimi\Pipe\Models\Credential $credential
 * @property-read \Fikrimi\Pipe\Models\Build $currentBuild
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereCommands($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereCredentialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereCurrentBuild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereDirDeploy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereDirWorkspace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereKeepBuild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereNamespace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereRepository($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Project extends BaseModel implements UserOwnable
{
    use HasCreator;

    public $incrementing = false;
    protected $casts = [
        'commands' => 'json',
    ];

    protected $fillable = [
        'id',
        'name',
        'branch',
        'credential_id',
        'repository',
        'host',
        'dir_deploy',
        'dir_workspace',
        'timeout',
        'commands',
        'current_build',
        'namespace',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (\Illuminate\Database\Eloquent\Model $model) {
            $model->id = Str::orderedUuid()->toString();
        });

        static::saving(function (\Illuminate\Database\Eloquent\Model $model) {
            if (! is_array($model->commands)) {
                $model->commands = preg_split("/\s\s+/", $model->commands);
            }
        });
    }

    public function credential()
    {
        return $this->belongsTo(Credential::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function builds()
    {
        return $this->hasMany(Build::class);
    }

    /**
     * @param $invoker
     * @return $this
     * @throws \Fikrimi\Pipe\Exceptions\ApplicationException
     */
    public function release($invoker)
    {
        $build = new Build();
        $build->invoker = $invoker;
        $build->branch = $this->branch;

        $this->builds()->save($build);

        return $this;
    }
}
