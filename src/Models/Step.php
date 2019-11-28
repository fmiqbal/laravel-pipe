<?php

namespace Fikrimi\Pipe\Models;

/**
 * Fikrimi\Pipe\Models\Step
 *
 * @property int $id
 * @property string $build_id
 * @property string $group
 * @property string $command
 * @property int|null $exit_status
 * @property string|null $output
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Fikrimi\Pipe\Models\Build $build
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step whereBuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step whereExitStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step whereOutput($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Step whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Step extends BaseModel
{
    protected $guarded = [];
}
