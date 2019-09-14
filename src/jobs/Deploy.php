<?php

namespace Fikrimi\Pipe\Jobs;

use Cache;
use Crypt;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Credential;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SSH;

class Deploy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var \Fikrimi\Pipe\Models\Project
     */
    private $build;

    /**
     * Create a new job instance.
     *
     * @param \Fikrimi\Pipe\Models\Build $build
     */
    public function __construct(Build $build)
    {
        $this->build = $build;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        $project = $this->build->meta_project;

        $cred = $project['credential'];
        $key = $this->build->getCacheKey();

        Cache::delete($key);

        SSH::connect([
            'host'     => $project['host'],
            'username' => $cred['username'],

            Credential::$typeAuth[$cred['type']] => Crypt::decrypt($cred['auth']),
        ])
            ->run([
                'cd ' . $project['dir_deploy'],
                'echo $?',
                'git pull',
                'echo $?',
            ], function ($line) use ($key) {
                Cache::put($key, (Cache::get($key) ?: '') . "\n" . $line);
            });
    }
}
