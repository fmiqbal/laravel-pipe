<?php

namespace Fikrimi\Pipe\Repositories;

use Crypt;
use Fikrimi\Pipe\Exceptions\ApplicationException;
use Fikrimi\Pipe\Models\Credential;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use RuntimeException;

class CredentialRepo extends Repository
{
    protected static $modelName = Credential::class;

    public function store(array $array = [])
    {
        if (! empty($array)) {
            $this->fromArray($array);
        }

        $this->model->auth = Crypt::encrypt($this->model->auth);
        $this->model->fingerprint = $this->getFingerprint();

        if (Credential::query()->where([
            'fingerprint' => $this->model->fingerprint,
            'type'        => $this->model->type,
            'username'    => $this->model->username,
        ])->exists()) {
            throw new RuntimeException('Credential sudah ada');
        }

        $this->model->save();

        return $this;
    }

    /**
     * @param $array
     * @return \Fikrimi\Pipe\Interfaces\RepositoryInterface
     * @throws \Fikrimi\Pipe\Exceptions\ApplicationException
     */
    public function fromArray($array)
    {
        if (! ($array instanceof Arrayable || is_array($array))) {
            throw new ApplicationException('Cannot create if not array or arrayable');
        }

        $this->model->fill([
            'username' => $array['username'],
            'type'     => $array['type'],
            'auth'     => $array['auth'],
        ]);

        return $this;
    }

    public function fromRequest(Request $request)
    {
        $this->model->fill([
            'username' => $request->get('username'),
            'type'     => $request->get('type'),
            'auth'     => $request->get('auth'),
        ]);

        return $this;
    }

    protected function getFingerprint()
    {
        return strtoupper(\Str::orderedUuid());
    }
}
