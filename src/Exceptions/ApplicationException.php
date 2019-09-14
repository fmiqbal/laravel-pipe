<?php

namespace Fikrimi\Pipe\Exceptions;

use Exception;
use Log;

class ApplicationException extends Exception
{
    /**
     * @var \Exception
     */
    protected $exception;

    public function __construct($e)
    {
        if (! $e instanceof \Exception) {
            $e = new \Exception($e);
        }

        parent::__construct($e);

        $this->exception = $e;
    }

    public function report()
    {
        Log::critical($this->exception);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function render()
    {
        if (config('app.env') === 'production') {
            return back()->withInput()->withErrors('Terjadi Kesalahan, Silahkan coba lagi');
        }

        throw $this->exception;
    }
}
