<?php

namespace Fikrimi\Pipe\Seeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pipe_stacks')->delete();

        $this->call(StacksTableSeeder::class);
    }
}
