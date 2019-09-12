<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->unsignedBigInteger('credential_id');
            $table->smallInteger('provider');
            $table->string('host');
            $table->string('dir_deploy');
            $table->string('dir_workspace');
            $table->text('namespace');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('credential_id')->references('id')
                ->on('credentials')
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
        Schema::dropIfExists('projects');
    }
}
