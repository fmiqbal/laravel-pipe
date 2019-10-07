<?php

namespace Fikrimi\Pipe\Jobs;

use Collective\Remote\Connection;
use Crypt;
use Exception;
use Fikrimi\Pipe\Enum\Provider;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Credential;
use Fikrimi\Pipe\Models\Step;
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

    public $timeout = 300;

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
     * @var string
     */
    private $signature;
    /**
     * @var \Fikrimi\Pipe\Models\Step
     */
    private $step;

    /**
     * Create a new job instance.
     *
     * @param \Fikrimi\Pipe\Models\Build $build
     * @throws \Pusher\PusherException
     */
    public function __construct(Build $build)
    {
        set_time_limit(300);

        $this->build = $build;
        $this->broadcaster = $this->getBroadcaster();
        $this->ssh = $this->getSSH($build->project, $build->project['credential']);
        $this->signature = hash('crc32', now() . $this->build->id);

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
     * @throws \Exception
     */
    public function handle()
    {
        $build = $this->build;
        $project = $build->meta_project;

        $dir = date('YmdHis-') . $build->id;
        $url = Provider::$repositoryUrlSsh[$project['provider']] . $project['namespace'];
        $branch = 'master';

        $commands = $this->prepCommands([
            'pipe-preparing-workspace' => [
                "\cd {$project['dir_workspace']}",
                'mkdir -p builds/base',
                '\cd builds/base',
                'git init',
                "git remote remove origin; git remote add origin {$url}",
                'git fetch',
                "git reset --hard origin/{$branch}",
                "\cd {$project['dir_workspace']}/builds",
                "rsync -aq base/ {$dir} --exclude .git",
                "\cd $dir",
            ],
            'build'                    => [
                'echo "this is building step"',
            ],
            'pipe-post-build'          => [
                "rm -rf {$project['dir_deploy']}",
                "ln -s {$project['dir_workspace']}/builds/{$dir} {$project['dir_deploy']}",
            ],
        ]);

        try {
            $this->ssh->run(
                $commands,
                function ($line) {
                    $this->buildHook($line);
                });
        } catch (Exception $e) {
            $this->step->update([
                'exit_status' => '1',
                'output'      => $e->getMessage(),
            ]);
        }

        $success = $this->build->steps()->where('exit_status', '<>', '0')->exists();

        $build->update([
            'status' => $success ? Build::S_SUCCESS : Build::S_FAILED,
        ]);

        $this->broadcaster->trigger('terminal-' . $build->id, 'finished', [
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
            'timeout'  => $this->timeout,
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

        $steps = [];

        foreach ($commands as $key => $group) {
            foreach ($group as $command) {
                $steps[] = $this->build->steps()->create([
                    'command' => $command,
                    'group'   => $key,
                ]);
            }
        }

        return collect($steps)
            ->map(function ($item) {
                return ''
                    . 'echo "pipe-signature-' . $this->signature . ' start ' . $item->id . '";'
                    . $item->command . ';'
                    . 'echo "pipe-signature-' . $this->signature . ' stop" $?';
            })
            ->flatten()
            ->toArray();
    }

    /**
     * @param $rawLine
     * @return void
     * @throws \Pusher\PusherException
     */
    private function buildHook($rawLine)
    {
        $lines = explode("\n", $rawLine);

        foreach ($lines as $line) {
            preg_match("[\S+]", $line, $sig);

            if (($sig[0] ?? '') === 'pipe-signature-' . $this->signature) {
                $sig = explode(' ', trim($line));
                if ($sig[1] === 'start') {
                    $this->step = Step::find($sig[2]);
                }

                if ($sig[1] === 'stop') {
                    $this->step->update([
                        'exit_status' => trim($sig[2]),
                    ]);
                }
                //dr($sig, $this->step);
            } else {
                $this->step->update([
                    'output' => $this->step->output . $line,
                ]);

                if ((explode('-', $this->step->group)[0] ?? '') !== 'pipe') {
                    $this->broadcaster->trigger('terminal-' . $this->build->id, 'output', [
                        'line' => $line,
                    ]);
                }
            }
        }
    }
}
