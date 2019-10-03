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
use Pusher\Pusher;
use SSH;

class ExecutePipeline implements ShouldQueue
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
     * @throws \Pusher\PusherException
     */
    public function handle()
    {
        $project = $this->build->meta_project;

        $cred = $project['credential'];
        $key = $this->build->getCacheKey();
        $id = $this->build->id;

        Cache::delete($key);

        //$checker = 'echo "' . $id . ' $?"';
        $checker = 'echo ' . $id . ' $?';

        $commands = collect([
            'echo "========= GO TO DEPLOY DIR =========="',
            'cd ' . $project['dir_deploy'],
            'echo "========= DEPLOYING =========="',
            'git pull',
        ]);

        $statuses = collect([]);

        $ssh = SSH::connect([
            'host'     => $project['host'],
            'username' => $cred['username'],

            Credential::$typeAuth[$cred['type']] => Crypt::decrypt($cred['auth']),
        ]);

        $pusher = new Pusher(
            '0d289eb62a8539cda514',
            'e29f55177c2ce50ecab9',
            '870492',
            [
                'cluster' => 'ap1',
                'useTLS'  => true,
            ]
        );

        $lines = [];

        try {
            $ssh->run(
                $commands
                    ->map(function ($item) use ($checker) {
                        return $item . ' && ' . $checker;
                    })
                    ->flatten()
                    ->toArray(),
                function ($line) use ($id, &$lines, &$statuses, &$last, $pusher) {
                    $status = explode(' ', $line);
                    if (($status[0] ?? '') === $id) {
                        $statuses[] = trim($status[1]);
                    } else {
                        $lines[] = $line;
                        $pusher->trigger('terminal-' . $id, 'output', [
                            'line' => $line,
                        ]);
                    }

                    $last = $line;
                });
        } catch (\Exception $e) {
            $this->build->update([
                'status' => Build::S_FAILED,
                'meta'   => [
                    'last_line' => $e->getMessage(),
                ],
            ]);

            $pusher->trigger('terminal-' . $id, 'finished', [
                'finished' => true
            ]);

            return;
        }

        $pusher->trigger('terminal-' . $id, 'finished', [
            'finished' => true
        ]);

        $statuses = $statuses->pad($commands->count(), '1');
        $commands = $commands->zip($statuses->toArray());

        $success = $commands->every(function ($item) {
            return $item[1] == 0;
        });

        $this->build->update([
            'meta'       => [
                'lines'     => $lines,
                'last_line' => $last,
            ],
            'meta_steps' => $commands->toArray(),
            'status'     => $success ? Build::S_SUCCESS : Build::S_FAILED,
        ]);
    }

    public function fail($exception = null)
    {
        if ($this->job) {
            $this->job->fail($exception);
        }
    }
}
