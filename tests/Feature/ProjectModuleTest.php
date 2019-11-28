<?php

namespace Fikrimi\Pipe\Tests\Feature;

use Fikrimi\Pipe\Models\Project;
use Fikrimi\Pipe\Tests\TestCase;
use Illuminate\Foundation\Auth\User;

class ProjectModuleTest extends TestCase
{
    /** @var \Fikrimi\Pipe\Models\Project */
    private $projectOwned;

    /** @var \Fikrimi\Pipe\Models\Project */
    private $projectOther;

    public function setUp(): void
    {
        parent::setUp();

        $this->projectOwned = factory(Project::class)->create();
        $this->projectOther = factory(Project::class)->create();

        $this->projectOwned->setCreator($this->user)->save();
        $this->projectOther->setCreator(factory(User::class)->create())->save();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_index_can_be_accessed()
    {
        $this->checkAuth('projects');

        $this->get('projects')
            ->assertStatus(200)
            ->assertSeeTextInOrder([
                'Projects',
                'new projects',
            ]);

        // Check for view other module
        config([
            'pipe.auth.policies.projects.view_other' => true,
        ]);

        $this->get('projects')
            ->assertSee($this->projectOwned->name)
            ->assertSee($this->projectOther->name);
    }

    public function test_index_not_showing_other_cred()
    {
        $this->checkAuth('projects');

        config([
            'pipe.auth.policies.projects.view_other' => false,
        ]);

        $this->get('projects')
            ->assertSee($this->projectOwned->name)
            ->assertDontSee($this->projectOther->name);
    }

    public function test_create_can_be_accessed()
    {
        $this->checkAuth('projects/create');

        $this->get('projects/create')
            ->assertSeeTextInOrder([
                'NEW PROJECT',
                // 'Repository',
                // 'Deploy Server',
                // 'Commands',
            ]);
    }

    public function test_user_can_create_new_project()
    {
        $this->actingAs($this->user);

        $project = factory(Project::class)->make();

        $this->post('projects', $project->toArray())
            ->assertRedirect('projects');

        $this->get('projects')
            ->assertSee($project['name']);
    }

    public function test_user_can_view_project()
    {
        $this->checkAuth('projects/' . $this->projectOwned->id);

        config([
            'pipe.auth.policies.projects.view_other' => true,
        ]);

        $this->get('projects/' . $this->projectOwned->id)
            ->assertStatus(200)
            ->assertSee($this->projectOwned->name);

        $this->get('projects/' . $this->projectOther->id)
            ->assertStatus(200)
            ->assertSee($this->projectOther->name);
    }

    public function test_user_cannot_view_project_owned_not_by_self()
    {
        $this->checkAuth('projects/' . $this->projectOther->id);

        config([
            'pipe.auth.policies.projects.view_other' => false,
        ]);

        $this->get('projects/' . $this->projectOther->id)
            ->assertStatus(403);
    }

    public function test_user_can_view_edit_project()
    {
        $this->checkAuth('projects/' . $this->projectOwned->id);

        $projectOwned = $this->createResource(Project::class, self::F_CREATE);

        $this->get('projects/' . $projectOwned->id . '/edit')
            ->assertStatus(200)
            ->assertSee($projectOwned->name);
    }

    public function test_user_can_update_project()
    {
        $this->checkAuth('projects/' . $this->projectOwned->id, 'put');

        $projectOwned = $this->createResource(Project::class, self::F_CREATE);

        $projectOwned->fill($this->createResource(Project::class, self::F_MAKE)->toArray());

        $this->put('projects/' . $projectOwned->id, $projectOwned->toArray())
            ->assertRedirect('projects/');

        $this->get('projects/')
            ->assertSee($projectOwned->name);
    }

    public function test_user_can_delete_project()
    {
        $projectOwned = $this->createResource(Project::class, self::F_CREATE);
        $projectOther = $this->createResource(Project::class, self::F_CREATE, 'other');

        $this->checkAuth('projects/' . $projectOwned->id, 'delete');

        config([
            'pipe.auth.policies.projects.delete_other' => true,
        ]);

        $this->delete('projects/' . $projectOwned->id)
            ->assertRedirect('projects')
            ->assertDontSee($projectOwned->name);

        $this->delete('projects/' . $projectOther->id)
            ->assertRedirect('projects')
            ->assertDontSee($projectOther->name);
    }

    public function test_user_cannot_delete_project_owned_not_by_self()
    {
        $projectOther = $this->createResource(Project::class, self::F_CREATE, 'other');

        $this->checkAuth('projects/' . $projectOther->id, 'delete');

        config([
            'pipe.auth.policies.projects.delete_other' => false,
        ]);

        $this->delete('projects/' . $projectOther->id)
            ->assertStatus(403);
    }
}
