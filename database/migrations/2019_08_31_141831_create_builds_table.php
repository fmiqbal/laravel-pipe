<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pipe_builds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->string('invoker');
            $table->string('branch');
            $table->string('commit_id')->nullable();
            $table->char('status');
            $table->text('errors')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('stopped_at')->nullable();
            $table->json('meta_project');
            $table->timestamps();

            $table->foreign('project_id')->references('id')
                ->on('pipe_projects')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });


        Schema::table('pipe_projects', function (Blueprint $table) {
            $table->uuid('current_build')->nullable();

            $table->foreign('current_build')->references('id')
                ->on('pipe_builds')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfexists('pipe_builds');
    }
}
