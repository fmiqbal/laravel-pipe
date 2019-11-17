<?php

namespace Fikrimi\Pipe\Jobs;

use Exception;
use Fikrimi\Pipe\Models\Build;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib\Net\SSH2;

class SwitchBuild extends Executor implements ShouldQueue
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
        $buildDir = date('YmdHis-') . $this->build->id;

        $commands = $this->prepCommands([
            'pipe-post-build' => [
                // remove deploy directory
                "\\rm -rf {$this->project->dir_deploy}",
                "\ln -s {$this->project->dir_workspace}/{$projectDir}/{$buildDir} {$this->project->dir_deploy}",
            ],
        ]);

        try {
            $ssh = $this->getSSH($this->project);

            $ssh->exec(
                implode(' && ', $commands)
            );

            $this->project->update([
                'current_build' => $this->build->id,
            ]);
        } catch (Exception $e) {
            isset($ssh) && $ssh->_close_channel(SSH2::CHANNEL_EXEC);
        }
    }
}
