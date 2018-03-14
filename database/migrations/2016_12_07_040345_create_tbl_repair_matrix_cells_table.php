<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblRepairMatrixCellsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblRepair_matrix_cell', function (Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('repair_matrix_id');
			$table->unsignedInteger('repair_method_id');
			$table->string('crack_condition');
			$table->string('rut_condition');
			$table->integer('type')->comment('1: AS, 2: BST, 3: CC');
			$table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
			
			$table->foreign('repair_matrix_id')->references('id')->on('tblRepair_matrix')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_repair_matrix_cells');
    }
}
