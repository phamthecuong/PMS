<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeyToCell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblRepair_matrix_cell_values', function (Blueprint $table) {
            $table->integer('repair_matrix_cell_id')->unsigned()->change();
            $table->foreign('repair_matrix_cell_id', 'cell_value')
                ->references('id')->on('tblRepair_matrix_cell')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblRepair_matrix_cell_values', function (Blueprint $table) {
            //
        });
    }
}
