<?php

namespace Fikrimi\Pipe\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Fikrimi\Pipe\Models\Credential
 *
 * @property int $id
 * @property string $username
 * @property int $type
 * @property string $fingerprint
 * @property string $auth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read \Illuminate\Foundation\Auth\User|null $creator
 * @property-read mixed $auth_decrypted
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential whereAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential whereFingerprint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Fikrimi\Pipe\Models\Credential whereUsername($value)
 * @mixin \Eloquent
 */
class Credential extends BaseModel implements UserOwnable
{
    use HasCreator;

    public const T_KEY = 0;
    public const T_PASS = 1;

    public static $typeNames = [
        self::T_KEY  => 'Private Key',
        self::T_PASS => 'Password',
    ];

    public static $typeAuth = [
        self::T_KEY  => 'keytext',
        self::T_PASS => 'password',
    ];

    protected $fillable = [
        'username',
        'type',
        'auth',
    ];

    public static function getAuth($credential)
    {
        return self::$typeAuth[$credential['type']];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (\Illuminate\Database\Eloquent\Model $model) {
            $model->auth = Crypt::encrypt($model->auth);
            $model->fingerprint = strtoupper(Str::uuid());
        });
    }

    public function getAuthDecryptedAttribute()
    {
        return \Illuminate\Support\Facades\Crypt::decrypt($this->auth);
    }
}
