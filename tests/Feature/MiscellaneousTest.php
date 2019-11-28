<?php

namespace Fikrimi\Pipe\Tests\Feature;

use Fikrimi\Pipe\Tests\TestCase;

class MiscellaneousTest extends TestCase
{
    public function test_base_route_redirect_to_project()
    {
        $this->actingAs($this->user);

        $this->get('/')
            ->assertRedirect(route('pipe::projects.index'));
    }

    public function test_application_exception_redirect_back()
    {
        $this->withExceptionHandling();
        $this->actingAs($this->user);

        $this->get('tests/application_exception', [
            'HTTP_REFERER' => 'projects/',
        ])
            ->assertRedirect('projects');
    }

    public function test_application_exception_thrown_if_development()
    {
        $this->withExceptionHandling();

        config([
            'app.env' => 'development',
        ]);

        $this->actingAs($this->user);

        $this->get('tests/application_exception')
            ->assertSee('ApplicationException');
    }
}
