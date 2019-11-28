<?php

namespace Fikrimi\Pipe\Tests\Feature;

use Fikrimi\Pipe\Models\Project;
use Fikrimi\Pipe\Models\Stack;
use Fikrimi\Pipe\Tests\TestCase;
use Illuminate\Foundation\Auth\User;

class StackModuleTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_index_can_be_accessed()
    {
        $this->checkAuth('stacks');

        $this->get('stacks')
            ->assertStatus(200)
            ->assertSeeTextInOrder([
                'Stacks',
                'new stacks',
            ]);
    }

    public function test_create_can_be_accessed()
    {
        $this->checkAuth('stacks/create');

        $this->get('stacks/create')
            ->assertSeeTextInOrder([
                'NEW STACKS',
            ]);
    }

    public function test_user_can_create_new_stack()
    {
        $this->checkAuth('stacks', 'post');

        $stack = factory(Stack::class)->make();

        $this->post('stacks', $stack->toArray())
            ->assertRedirect('stacks');

        $this->get('stacks')
            ->assertSee($stack['name']);
    }

    public function test_user_can_duplicate_existing_stack()
    {
        $this->checkAuth('stacks', 'post');

        $stack = factory(Stack::class)->create();

        $this->post("stacks/$stack->id/duplicate")
            ->assertRedirect('stacks');

        $this->get('stacks')
            ->assertSeeInOrder([
                $stack['name'],
                $stack['name'],
            ]);
    }

    public function test_show_stack_is_json_only()
    {
        $stack = $this->createResource(Stack::class, self::F_CREATE);

        $this->checkAuth('stacks/' . $stack->id);

        $this->getJson('stacks/' . $stack->id)
            ->assertSee($stack->name);

        $this->get('stacks/' . $stack->id)
            ->assertStatus(404);
    }

    public function test_user_can_view_edit_stack_form()
    {
        $stack = $this->createResource(Stack::class, self::F_CREATE);

        $this->checkAuth('stacks/' . $stack->id);

        $this->get('stacks/' . $stack->id . '/edit')
            ->assertStatus(200)
            ->assertSee($stack->name);
    }

    public function test_user_can_update_stack()
    {
        $stack = $this->createResource(Stack::class, self::F_CREATE);

        $this->checkAuth('stacks/' . $stack->id, 'put');

        $stack->fill($this->createResource(Stack::class, self::F_MAKE)->toArray());

        $this->put('stacks/' . $stack->id, $stack->toArray())
            ->assertRedirect('stacks/');

        $this->get('stacks/')
            ->assertSee($stack->name);
    }

    public function test_user_can_delete_stack()
    {
        $stack = $this->createResource(Stack::class, self::F_CREATE);

        $this->checkAuth('stacks/' . $stack->id, 'delete');

        $this->delete('stacks/' . $stack->id)
            ->assertRedirect('stacks')
            ->assertDontSee($stack->name);
    }
}
