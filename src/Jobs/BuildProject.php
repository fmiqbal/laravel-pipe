<?php

namespace Fikrimi\Pipe\Jobs;

use Carbon\Carbon;
use Exception;
use Fikrimi\Pipe\Enum\Repository;
use Fikrimi\Pipe\Exceptions\ApplicationException;
use Fikrimi\Pipe\Exceptions\TerminationException;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Step;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use phpseclib\Net\SSH2;

class BuildProject extends Executor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var bool
     */
    public $timeout = 600;
    /**
     * @var \Fikrimi\Pipe\Models\Step
     */
    protected $step;
    protected $status;

    /**
     * Create a new job instance.
     *
     * @param \Fikrimi\Pipe\Models\Build $build
     */
    public function __construct(Build $build)
    {
        parent::__construct($build);

        $this->timeout = $this->project->timeout;
        set_time_limit($this->project->timeout);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $projectDir = 'projects-' . $this->build->project->id;
        $buildDir = $this->build->created_at->format('YmdHis') . '-' . $this->build->id;
        $url = Repository::$sshURL[$this->project->repository] . $this->project->namespace;

        $keepBuilds = Build::latest()->limit($this->project->keep_build)->pluck('id')->toArray();
        $removeCommands = '';
        foreach (array_merge(['base'], $keepBuilds) as $keepBuild) {
            $removeCommands .= "| grep -v '$keepBuild'";
        }

        $commands = $this->prepCommands([
            'pipe-preparing-workspace' => [
                // make directory
                "\cd {$this->project->dir_workspace}",
                "\mkdir -p {$projectDir}/base",

                // checkout git
                "\cd {$projectDir}/base",
                'git init',
                "git remote remove origin; git remote add origin {$url}",
                'git fetch',
                "git reset --hard origin/{$this->build->branch}",

                // copy to new folder
                "\cd {$this->project->dir_workspace}/{$projectDir}",
                "\\rsync -aq base/ {$buildDir} --exclude .git",
                "\cd $buildDir",
            ],

            'build' => $this->project->commands,

            'pipe-post-build' => [
                // remove deploy directory
                "\\rm -rf {$this->project->dir_deploy}",
                "\ln -s {$this->project->dir_workspace}/{$projectDir}/{$buildDir} {$this->project->dir_deploy}",
            ],
        ]);

        try {
            $ssh = $this->getSSH($this->project);
            $this->build->update([
                'status'     => Build::S_RUNNING,
                'started_at' => Carbon::now(),
            ]);

            $ssh->exec(
                implode(' && ', $commands),
                function ($line) {
                    $this->buildHook($line);

                    // check for in-build termination
                    if ((int) Cache::get($this->build->getCacheKey('status')) === Build::S_PENDING_TERM) {
                        throw new TerminationException('Terminated by user');
                    }
                });

            $ssh->exec(
                implode(' && ', [
                    // clean old build
                    "\cd {$this->project->dir_workspace}/{$projectDir}",
                    "\\rm -rf `ls $removeCommands`",
                ])
            );
        } catch (Exception $e) {
            isset($ssh) && $ssh->_close_channel(SSH2::CHANNEL_EXEC);

            $this->build->update([
                'errors'     => $e->getMessage(),
                'status'     => $e instanceof TerminationException ? Build::S_TERMINATED : Build::S_FAILED,
                'stopped_at' => Carbon::now(),
            ]);

            $this->build->steps()->whereNull('exit_status')->update([
                'exit_status' => 1,
            ]);
        }

        if (! isset($e)) {
            $failed = $this->build->steps()->where('exit_status', '<>', '0')->exists();

            if (! $failed) {
                $this->project->update([
                    'current_build' => $this->build->id
                ]);
            }

            $this->build->update([
                'status'     => $failed ? Build::S_FAILED : Build::S_SUCCESS,
                'stopped_at' => Carbon::now(),
            ]);
        }

        $this->broadcaster->trigger('terminal-' . $this->build->id, 'finished', [
            'finished' => true,
        ]);
    }

    /**
     * @param $rawLine
     * @return void
     * @throws \Pusher\PusherException
     * @throws \Fikrimi\Pipe\Exceptions\ApplicationException
     */
    protected function buildHook($rawLine)
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
                        throw new ApplicationException($line);
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
