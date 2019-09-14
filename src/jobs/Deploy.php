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
        $id = $this->build->id;

        Cache::delete($key);

        $checker = 'echo "$DEPLOY_TOKEN $?"';

        $commands = collect([
            "export DEPLOY_TOKEN=$id",
            'cd ' . $project['dir_deploy'],
            //'git pull',
            //'echo $?',
        ]);

        $statuses = [];

        SSH::connect([
            'host'     => $project['host'],
            'username' => $cred['username'],

            Credential::$typeAuth[$cred['type']] => Crypt::decrypt($cred['auth']),
        ])
            ->run(
                $commands
                    ->map(function ($item) use ($checker) {
                        return [$item, $checker];
                    })
                    ->flatten()
                    ->toArray(),
                function ($line) use ($id, &$statuses) {
                    $status = explode(' ', $line);
                    if ($status[0] ?? '' === $id) {
                        $statuses[] = trim($status[1]);
                    }
                    //Cache::put($key, (Cache::get($key) ?: '') . "\n" . $line);
                });
        $commands = $commands->zip($statuses);

        $this->build->update([
            'meta_steps' => $commands->toArray(),
            'status'     => Build::S_SUCCESS,
        ]);
    }
}
