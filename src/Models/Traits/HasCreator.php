<?php

namespace Fikrimi\Pipe\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;

trait HasCreator
{
    private $autoCreator = true;

    public static function bootHasCreator()
    {
        static::creating(function ($model) {
            if ($model->autoCreator && method_exists($model, 'setCreator')) {
                $model->setCreator();
            }
        });
    }

    /**
     * @param $status
     * @return void
     */
    public function setAutoCreator($status): void
    {
        $this->autoCreator = $status;
    }

    public function setCreator(User $user = null)
    {
        if ($user === null) {
            $user = User::find(auth()->id());
        }

        $this->forceFill([
            $this->getCreatorColumn() => $user[$this->getCreatorPrimaryKey()],
        ]);

        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->getCreatorColumn(), $this->getCreatorPrimaryKey());
    }

    public function getCreatorColumn()
    {
        return 'created_by';
    }

    public function getCreatorPrimaryKey()
    {
        return 'id';
    }
}
