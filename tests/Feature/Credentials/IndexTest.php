<?php

namespace Tests\Credentials;

use Tests\TestCase;

class IndexTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_index_can_be_accessed()
    {
        \Route::get('test', function () {
            return "asdkfj";
        });

        $response = $this->get('/test');
        $response->assertSee('haha');

        // if (config('pipe.modules.auth')) {
        //     $response->assertStatus(302);
        // } else {
        //     $response->assertStatus(200);
        // }
    }
}
