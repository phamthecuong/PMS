<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumRutCrackTblRepairMatrixCell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblRepair_matrix_cell', function (Blueprint $table) {
             $table->dropColumn(array('crack_from', 'crack_to', 'rut_from', 'rut_to'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblRepair_matrix_cell', function (Blueprint $table) {
            //
        });
    }
}
