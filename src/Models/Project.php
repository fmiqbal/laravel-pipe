<?php

namespace Fikrimi\Pipe\Models;

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
    public $incrementing = false;
    protected $guarded = [];

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
}
