<?php

namespace Fikrimi\Pipe\Tests\Feature;

use Fikrimi\Pipe\Jobs\BuildProject;
use Fikrimi\Pipe\Jobs\SwitchBuild;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Project;
use Fikrimi\Pipe\Tests\TestCase;
use Illuminate\Support\Facades\Bus;

class BuildModuleTest extends TestCase
{
    /**
     * @var Project $project
     */
    private $project;

    public function setUp(): void
    {
        parent::setUp();

        $this->project = $this->createResource(Project::class, self::F_CREATE, $this->user);
    }

    public function test_build_can_be_created()
    {
        $this->actingAs($this->user);

        Bus::fake();

        $this->post('projects/' . $this->project->id . '/build')
            ->assertRedirect('projects/' . $this->project->id);

        $this->get('projects/' . $this->project->id)
            ->assertSee($this->project->builds()->first()->id);
    }

    public function test_build_can_invoke_pipeline()
    {
        $this->actingAs($this->user);

        Bus::fake();

        $project = factory(Project::class)->create()
            ->release('manual');

        Bus::assertDispatched(BuildProject::class, function (BuildProject $job) use ($project) {
            return $job->build->project_id === $project->id;
        });
    }

    public function test_build_can_be_switched()
    {
        Bus::fake();

        $project = $this->createResource(Project::class, self::F_CREATE, $this->user)
            ->release('manual');

        $build = $project->builds->first();

        $this->checkAuth("builds/$build->id/switch", 'post');

        $this->post("builds/$build->id/switch")
            ->assertRedirect('projects/' . $project->id);

        Bus::assertDispatched(SwitchBuild::class, function (SwitchBuild $job) use ($build) {
            return $job->build->id === $build->id;
        });
    }

    public function test_user_can_view_build()
    {
        Bus::fake();
        $project = $this->project
            ->release('manual');

        $otherProject = $this->createResource(Project::class, self::F_CREATE)
            ->release('manual');

        $build = $project->builds->first();
        $otherBuild = $otherProject->builds->first();
        $this->checkAuth('builds/' . $build->id);

        config([
            'pipe.auth.policies.projects.view_other' => true,
        ]);

        $this->get('builds/' . $build->id)
            ->assertStatus(200)
            ->assertSee($build->name);

        $this->get('builds/' . $otherBuild->id)
            ->assertStatus(200)
            ->assertSee($otherBuild->name);
    }

    public function test_build_can_be_terminated()
    {
        Bus::fake();

        $project = $this->createResource(Project::class, self::F_CREATE, $this->user)
            ->release('manual');

        $build = $project->builds->first();

        $this->checkAuth('builds/' . $build->id, 'delete');

        $this->delete('builds/' . $build->id)
            ->assertRedirect('projects/' . $project->id);

        $this->assertEquals($project->builds()->first()->status, Build::S_PENDING_TERM);

        $build = $project->builds->first();
        $build->update([
            'status' => Build::S_SUCCESS,
        ]);

        $this->delete('builds/' . $build->id)
            ->assertRedirect('projects/' . $project->id);

        $this->assertEquals($project->builds()->first()->status, Build::S_SUCCESS);
    }
}
