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
        $build = $this->build;
        $project = $build->meta_project;

        $cred = $project['credential'];
        $key = $build->getCacheKey();
        $id = $build->id;

        $broadcaster = $this->getBroadcaster();
        $ssh = $this->getSSH($project, $cred);

        Cache::delete($key);

        $dir = date('YmdHis-') . $build->id;
        $url = \Fikrimi\Pipe\Enum\Provider::$repositoryUrlSsh[$project['provider']] . $project['namespace'];
        $branch = 'master';

        $commands = collect([
            // preparing
            "cd {$project['dir_workspace']}",
            'mkdir -p builds/base',
            'cd builds/base',
            'git init',
            "git remote remove origin; git remote add origin {$url}",
            'git fetch',
            "git reset --hard origin/{$branch}",
            'cd ..',
            "rsync -aq base/ {$dir} --exclude .git",
            "cd $dir",
            // building

            // linking
            "rm -rf {$project['dir_deploy']}",
            "ln -s {$project['dir_workspace']}/builds/{$dir} {$project['dir_deploy']}",
        ]);

        $statuses = collect([]);
        $lines = [];

        $success = null;
        try {
            $ssh->run(
                $this->prepCommands($commands),
                function ($line) use ($id, &$lines, &$statuses, &$last, $broadcaster) {
                    $status = explode(' ', $line);

                    if (($status[0] ?? '') === $id) {
                        $statuses[] = trim($status[1]);
                    } else {
                        $lines[] = $line;
                        $broadcaster->trigger('terminal-' . $id, 'output', [
                            'line' => $line,
                        ]);
                    }

                    $last = $line;
                });
        } catch (\Exception $e) {
            $last = $e->getMessage();
            $success = false;
        }

        $statuses = $statuses->pad($commands->count(), '1');
        $commands = $commands->zip($statuses->toArray());

        if ($success !== false) {
            $success = $commands->every(function ($item) {
                return $item[1] == 0;
            });
        }

        $build->update([
            'meta'       => [
                'lines'     => $lines,
                'last_line' => $last,
            ],
            'meta_steps' => $commands->toArray(),
            'status'     => $success ? Build::S_SUCCESS : Build::S_FAILED,
        ]);

        $broadcaster->trigger('terminal-' . $id, 'finished', [
            'finished' => true,
        ]);
    }

    /**
     * @return \Pusher\Pusher
     * @throws \Pusher\PusherException
     */
    private function getBroadcaster(): \Pusher\Pusher
    {
        $broadcaster = new Pusher(
            '0d289eb62a8539cda514',
            'e29f55177c2ce50ecab9',
            '870492',
            [
                'cluster' => 'ap1',
                'useTLS'  => true,
            ]
        );

        return $broadcaster;
    }

    /**
     * @param $project
     * @param $cred
     * @return \Collective\Remote\Connection
     */
    private function getSSH($project, $cred): \Collective\Remote\Connection
    {
        $ssh = SSH::connect([
            'host'     => $project['host'],
            'username' => $cred['username'],

            Credential::$typeAuth[$cred['type']] => Crypt::decrypt($cred['auth']),
        ]);

        return $ssh;
    }

    private function prepCommands($commands)
    {
        if (! $commands instanceof \Illuminate\Support\Collection) {
            $commands = collect($commands);
        }

        // Check last status
        $checker = 'echo ' . $this->build->id . ' $?';

        return collect($commands)
            ->map(function ($item) use ($checker) {
                return "echo \"executing $item\";" . $item . '; ' . $checker;
            })
            ->flatten()
            ->toArray();
    }
}
