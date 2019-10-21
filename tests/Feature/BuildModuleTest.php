<?php

namespace Fikrimi\Pipe\Tests\Feature;

use Fikrimi\Pipe\Jobs\ExecutePipeline;
use Fikrimi\Pipe\Models\Build;
use Fikrimi\Pipe\Models\Project;
use Fikrimi\Pipe\Tests\TestCase;
use Illuminate\Support\Facades\Bus;

class BuildModuleTest extends TestCase
{
    public function test_build_can_be_created()
    {
        $this->actingAs($this->user);

        Bus::fake();

        $project = factory(Project::class)->create();

        $this->post('projects/' . $project->id . '/build')
            ->assertRedirect('projects/' . $project->id);

        $this->get('projects/' . $project->id)
            ->assertSee($project->builds()->first()->id);
    }

    public function test_build_can_invoke_pipeline()
    {
        $this->actingAs($this->user);

        Bus::fake();

        $project = factory(Project::class)->create();
        $build = (new Build())->forceFill(['invoker' => 'unit_testing']);

        $project->builds()->save($build);

        Bus::assertDispatched(ExecutePipeline::class, function (ExecutePipeline $job) use ($project) {
            return $job->build->project_id === $project->id;
        });
    }
}
