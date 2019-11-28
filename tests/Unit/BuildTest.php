<?php

namespace Fikrimi\Pipe\Tests\Feature;

use Fikrimi\Pipe\Models\Project;
use Fikrimi\Pipe\Tests\TestCase;

class BuildTest extends TestCase
{
    public function test_id_generate_valid_uuid_when_saved()
    {
        $projectData = factory(Project::class)->create();

        /**
         * @url https://stackoverflow.com/questions/19989481/how-to-determine-if-a-string-is-a-valid-v4-uuid
         */
        $UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

        $this->assertRegExp($UUIDv4, $projectData->id);
    }
}
