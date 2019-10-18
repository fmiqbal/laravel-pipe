<?php

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCreator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \Doctrine\DBAL\Schema\Column $column */
        $column = DB::connection()->getDoctrineColumn(config('pipe.auth.table_name'), config('pipe.auth.primary_key'));

        Schema::table('pipe_credentials', function (Blueprint $table) use ($column) {
            $this->addCreator($table, $column);
        });

        Schema::table('pipe_projects', function (Blueprint $table) use ($column) {
            $this->addCreator($table, $column);
        });
    }

    public function addCreator(Blueprint $table, Column $column)
    {
        $table->addColumn($this->getLaravelColumnType($column), 'created_by', [
            'nullable' => ! $column->getNotnull(),
            'unsigned' => $column->getUnsigned(),
            'length'   => $column->getLength(),
        ]);

        $table->foreign('created_by')->references(config('pipe.auth.primary_key'))
            ->on(config('pipe.auth.table_name'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfexists('pipe_stacks');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     * @return string
     */
    public function getLaravelColumnType(Column $column)
    {
        $type = $column->getType()->getName();

        switch ($type) {
            case 'bigint':
                return 'biginteger';
            case 'smallint':
                return 'smallinteger';
            case 'blob':
                return 'binary';
        }

        return $type;
    }
}
