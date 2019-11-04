<?php

use Illuminate\Database\Migrations\Migration;

class Seeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class'          => \Fikrimi\Pipe\Seeder\DatabaseSeeder::class,
        ]);
    }

    public function down()
    {
        //     nothing to run
    }
}
