<?php

namespace Fikrimi\Pipe\Jobs;

use Cache;
use Crypt;
use Exception;
use Fikrimi\Pipe\Enum\Provider;
use Fikrimi\Pipe\Exceptions\TerminationException;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Credential;
use Fikrimi\Pipe\Models\Project;
use Fikrimi\Pipe\Models\Step;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use Pusher\Pusher;

class ExecutePipeline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;

    /**
     * @var \Fikrimi\Pipe\Models\Project
     */
    private $build;
    /**
     * @var \Pusher\Pusher
     */
    private $broadcaster;
    /**
     * @var string
     */
    private $signature;
    /**
     * @var \Fikrimi\Pipe\Models\Step
     */
    private $step;
    /**
     * @var \Fikrimi\Pipe\Models\Project
     */
    private $project;
    private $status;

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
        $this->project = (new Project())
            ->forceFill($build->meta_project);
        $this->broadcaster = $this->getBroadcaster();
        $this->signature = hash('crc32', now() . $this->build->id);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $dir = date('YmdHis-') . $this->build->id;
        $url = Provider::$repositoryUrlSsh[$this->project->provider] . $this->project->namespace;

        $branch = 'master';

        $commands = $this->prepCommands([
            'pipe-preparing-workspace' => [
                "\cd {$this->project->dir_workspace}",
                'mkdir -p builds/base',
                '\cd builds/base',
                'git init',
                "git remote remove origin; git remote add origin {$url}",
                'git fetch',
                "git reset --hard origin/{$branch}",
                "\cd {$this->project->dir_workspace}/builds",
                "rsync -aq base/ {$dir} --exclude .git",
                "\cd $dir",
            ],
            'build'                    => [
                'echo "this is building step"',
            ],
            'pipe-post-build'          => [
                "rm -rf {$this->project->dir_deploy}",
                "ln -s {$this->project->dir_workspace}/builds/{$dir} {$this->project->dir_deploy}",
            ],
        ]);

        $ssh = $this->getSSH($this->project);
        try {
            $ssh->exec(
                implode(' && ', $commands),
                function ($line) use ($ssh) {
                    $this->buildHook($line);

                    if ((int) Cache::get($this->build->getCacheKey('status')) === Build::S_PENDING_TERM) {
                        $ssh->_close_channel(SSH2::CHANNEL_EXEC);
                        throw new TerminationException('Terminated by user');
                    }
                });
        } catch (Exception $e) {
            $this->build->update([
                'errors' => $e->getMessage(),
            ]);

            $this->build->steps()->whereNull('exit_status')->update([
                'exit_status' => 1,
            ]);

            $this->build->update([
                'status' => $e instanceof TerminationException ? Build::S_TERMINATED : Build::S_FAILED,
            ]);
        }

        if (! isset($e)) {
            $failed = $this->build->steps()->where('exit_status', '<>', '0')->exists();

            $this->build->update([
                'status' => $failed ? Build::S_FAILED : Build::S_SUCCESS,
            ]);
        }

        $this->broadcaster->trigger('terminal-' . $this->build->id, 'finished', [
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
     * @param \Fikrimi\Pipe\Models\Project $project
     * @return \phpseclib\Net\SSH2
     */
    private function getSSH(Project $project)
    {
        $ssh = new SSH2($project->host);
        $auth = Crypt::decrypt($project->credential->auth);

        if (Credential::T_KEY) {
            $auth = (new RSA())->loadKey($auth);
        }

        $ssh->setTimeout(300);
        $ssh->login($project->credential->username, $auth);

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
                $sig = explode(' ', $line);

                if ($sig[1] === 'start') {
                    $this->step = Step::find($sig[2]);
                }

                if ($sig[1] === 'stop') {
                    $this->step->update([
                        'exit_status' => trim($sig[2]),
                    ]);

                    if (trim($sig[2]) != 0) {
                        throw new \Fikrimi\Pipe\Exceptions\ApplicationException($line);
                    }
                }

                continue;
            }

            $this->step->update([
                'output' => $this->step->output . $line,
            ]);

            // broadcast only if not signature
            if ((explode('-', $this->step->group)[0] ?? '') !== 'pipe') {
                $this->broadcaster->trigger('terminal-' . $this->build->id, 'output', [
                    'line' => $line,
                ]);
            }
        }
    }
}
