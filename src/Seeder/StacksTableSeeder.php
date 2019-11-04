<?php

namespace Fikrimi\Pipe\Seeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StacksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pipe_stacks')->insert([
            'name'        => 'Laravel',
            'description' => 'For laravel',
            'commands'    => json_encode([
                'npm install',
                'npm run prod',
                'composer install',
                'php artisan migrate --force',
                'php artisan db:seed --force',
                'php artisan config:cache',
            ]),
        ]);

        DB::table('pipe_stacks')->insert([
            'name'        => 'Standard',
            'description' => 'Nothing executed, just pull and deploy',
            'commands'    => json_encode([
            ]),
        ]);

        DB::table('pipe_stacks')->insert([
            'name'        => 'NPM',
            'description' => 'For pm standard',
            'commands'    => json_encode([
                'npm install',
                'npm run build',
            ]),
        ]);
    }
}
