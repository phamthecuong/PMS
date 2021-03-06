<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblRepairMatrixCellValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblRepair_matrix_cell_values', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('repair_matrix_cell_id');
			$table->integer('parameter_id');
        	$table->string('value');
			$table->integer('created_by')->nullable();
        	$table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_repair_matrix_cell_values');
    }
}
