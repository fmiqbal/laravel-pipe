<?php

namespace Fikrimi\Pipe\Jobs;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Exception;
use Fikrimi\Pipe\Exceptions\ApplicationException;
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

abstract class Executor
{
    /**
     * @var \Fikrimi\Pipe\Models\Project
     */
    public $build;
    /**
     * @var \Fikrimi\Pipe\Models\Project
     */
    protected $project;
    /**
     * @var \Pusher\Pusher
     */
    protected $broadcaster;
    /**
     * @var string
     */
    protected $signature;

    public function __construct(Build $build)
    {
        $this->build = $build;
        $this->project = (new Project())
            ->forceFill($build->meta_project);

        $this->signature = hash('crc32', now() . $this->build->id);

        $this->broadcaster = $this->getBroadcaster();
    }

    /**
     * @param \Fikrimi\Pipe\Models\Project $project
     * @return \phpseclib\Net\SSH2
     * @throws \Exception
     */
    public static function getSSH(Project $project)
    {
        $ssh = new SSH2($project->host);
        $auth = Crypt::decrypt($project->credential->auth);

        if (Credential::T_KEY) {
            $auth = (new RSA())->loadKey($auth);
        }

        $ssh->setTimeout($project->timeout);
        $login = $ssh->login($project->credential->username, $auth);

        if (! $login) {
            throw new \RuntimeException('Login Failed');
        }

        return $ssh;
    }

    /**
     * @return \Pusher\Pusher
     * @throws \Pusher\PusherException
     */
    protected function getBroadcaster(): Pusher
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

    protected function prepCommands($commands)
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
}
