<?php

namespace Fikrimi\Pipe\Jobs;

use Cache;
use Collective\Remote\Connection;
use Crypt;
use Exception;
use Fikrimi\Pipe\Enum\Provider;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Credential;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
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
     * @var \Collective\Remote\Connection
     */
    private $ssh;
    /**
     * @var \Pusher\Pusher
     */
    private $broadcaster;
    /**
     * @var array
     */
    private $meta;

    /**
     * Create a new job instance.
     *
     * @param \Fikrimi\Pipe\Models\Build $build
     * @throws \Pusher\PusherException
     */
    public function __construct(Build $build)
    {
        $this->build = $build;
        $this->broadcaster = $this->getBroadcaster();
        $this->ssh = $this->getSSH($build->project, $build->project['credential']);

        $this->meta = [
            'statuses' => collect([]),
            'lines'    => [],
            'success'  => null,
        ];
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
        /*
         * Yang harus di remake, harusnya struktur per command nya dirubah
         * bisa jadi satu command itu jadi object, isinya:
         * command yang di eksekusi
         * output nya
         * status nya
         * */

        $build = $this->build;
        $project = $build->meta_project;

        $key = $build->getCacheKey();
        $id = $build->id;

        Cache::delete($key);

        $dir = date('YmdHis-') . $build->id;
        $url = Provider::$repositoryUrlSsh[$project['provider']] . $project['namespace'];
        $branch = 'master';

        $preBuild = collect([
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
        ]);

        $build = collect([
            // building
        ]);

        $postBuild = collect([
            "rm -rf {$project['dir_deploy']}",
            "ln -s {$project['dir_workspace']}/builds/{$dir} {$project['dir_deploy']}",
        ]);

        try {
            $this->ssh->run($this->prepCommands($preBuild));

            $this->ssh->run(
                $this->prepCommands($build),
                function ($line) {
                    $this->buildHook($line);
                });

            $this->ssh->run($this->prepCommands($postBuild));
        } catch (Exception $e) {
            $last = $e->getMessage();
            $success = false;
        }

        //$statuses = $statuses->pad($build->count(), '1');
        //$build = $build->zip($statuses->toArray());
        //
        //if ($success !== false) {
        //    $success = $build->every(function ($item) {
        //        return $item[1] == 0;
        //    });
        //}
        //
        //$build->update([
        //    'meta'       => [
        //        'lines'     => $lines,
        //        'last_line' => $last,
        //    ],
        //    'meta_steps' => $build->toArray(),
        //    'status'     => $success ? Build::S_SUCCESS : Build::S_FAILED,
        //]);

        $this->broadcaster->trigger('terminal-' . $id, 'finished', [
            'finished' => true,
        ]);
    }

    /**
     * @return \Pusher\Pusher
     * @throws \Pusher\PusherException
     */
    private function getBroadcaster(): Pusher
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
    private function getSSH($project, $cred): Connection
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
        if (! $commands instanceof Collection) {
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

    /**
     * @param $line
     * @return array
     * @throws \Pusher\PusherException
     */
    private function buildHook($line)
    {
        $status = explode(' ', $line);

        //$status, $id, $statuses, $lines, $broadcaster
        if (($status[0] ?? '') === $this->build->id) {
            $statuses[] = trim($status[1]);
        } else {
            $lines[] = $line;
            $this->broadcaster->trigger('terminal-' . $this->build->id, 'output', [
                'line' => $line,
            ]);
        }

        return [$statuses, $lines];
    }

}
