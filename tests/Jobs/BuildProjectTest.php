<?php

namespace Fikrimi\Pipe\Tests\Jobs;

use Fikrimi\Pipe\Jobs\Executor;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Credential;
use Fikrimi\Pipe\Models\Project;
use Fikrimi\Pipe\Tests\TestCase;

class BuildProjectTest extends TestCase
{
    /**
     * @var \Fikrimi\Pipe\Models\Project
     */
    private $project;

    protected function setUp(): void
    {
        parent::setUp();

        $projectMock = include __DIR__ . '/../Mocks/projects.php';

        $credential = new Credential($projectMock['credentials']);
        $credential->save();

        $project = new Project($projectMock);
        $project->credential_id = $credential->id;
        $project->save();

        $this->project = $project;

        $this->ssh = Executor::getSSH($project);
    }

    public function test_build_create_required_directories()
    {
        $project = $this->project;
        $ssh = $this->ssh;

        $project->release('manual');

        $build = $project->builds()->first();

        $this->assertEquals(
            'projects-' . $project->id,
            trim($ssh->exec("ls $project->dir_workspace -1 | head -1"))
        );

        $this->assertEquals(
            $build->created_at->format('YmdHis') . '-' . $build->id,
            trim($ssh->exec("ls $project->dir_workspace/projects-$project->id -1 | sed -n 1p"))
        );

        $this->assertEquals(
            'base',
            trim($ssh->exec("ls $project->dir_workspace/projects-$project->id -1 | sed -n 2p"))
        );
    }

    public function test_build_command_success()
    {
        $project = $this->project;

        $successCommand = 'echo "this is first command"';
        $project->commands = [
            $successCommand,
        ];
        $project->save();

        $project->release('manual');
        $build = $project->builds()->latest()->first();

        $this->assertDatabaseHas('pipe_steps', [
            'command'     => $successCommand,
            'exit_status' => 0,
            'output'      => 'this is first command',
        ]);

        $project = Project::find($project->id);
        $this->assertEquals($build->id, $project->current_build);
        $this->assertEquals(Build::S_SUCCESS, $build->status);
    }

    public function test_build_command_failed()
    {
        $project = $this->project;

        $failedCommand = 'false';
        $project->commands = [
            $failedCommand,
        ];
        $project->save();

        $project->release('manual');
        $build = $project->builds()->latest()->first();

        $this->assertDatabaseHas('pipe_steps', [
            'command'     => $failedCommand,
            'exit_status' => 1,
        ]);

        $project = Project::find($project->id);

        $this->assertNotEquals($build->id, $project->current_build);
        $this->assertEquals(Build::S_FAILED, $build->status);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $ssh = $this->ssh;
        $dir = $this->project->dir_workspace;

        $ssh->exec("\\rm -rf $dir/*");
    }
}
