<?php

namespace Fikrimi\Pipe\Models;

use Fikrimi\Pipe\Models\Traits\HasCreator;
use Illuminate\Support\Str;

/**
 * Fikrimi\Pipe\Models\Project
 *
 * @property string $id
 * @property string $name
 * @property int $credential_id
 * @property int $provider
 * @property string $host
 * @property string $dir_deploy
 * @property string $dir_workspace
 * @property string $namespace
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Fikrimi\Pipe\Models\Build[] $builds
 * @property-read int|null $builds_count
 * @property-read \Fikrimi\Pipe\Models\Credential $credential
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereCredentialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereDirDeploy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereDirWorkspace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereNamespace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Project whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Project extends BaseModel
{
    use HasCreator;

    public $incrementing = false;
    protected $casts = [
        'commands' => 'json',
    ];

    protected $fillable = [
        'id',
        'name',
        'credential_id',
        'repository',
        'host',
        'dir_deploy',
        'dir_workspace',
        'timeout',
        'commands',
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

    public function release($invoker)
    {
        $build = new Build();
        $build->invoker = $invoker;

        $this->builds()->save($build);

        return $this;
    }
}
