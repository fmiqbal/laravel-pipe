<?php

namespace Fikrimi\Pipe\Tests\Feature\Credentials;

use Fikrimi\Pipe\Tests\TestCase;

class IndexTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_index_can_be_accessed()
    {
        $this->checkAuth('');

        $response = $this->get('/pipe');

        $response->assertSee('Dashboard');
    }
}
