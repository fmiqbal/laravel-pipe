<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pipe_steps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('build_id');
            $table->string('command');
            $table->unsignedSmallInteger('exit_status');
            $table->text('output');
            $table->timestamps();

            $table->foreign('build_id')->references('id')
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
        Schema::dropIfexists('pipe_steps');
    }
}
