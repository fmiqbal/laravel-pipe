<?php

namespace Fikrimi\Pipe\Models\Traits;

use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCreator
{
    private $autoCreator = true;

    public static function bootWithCreator()
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

        $this->creator()->associate($user);

        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->getCreatorColumn(), 'id');
    }

    public function getCreatorColumn()
    {
        return 'created_by';
    }
}
