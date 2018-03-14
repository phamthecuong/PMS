<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangTblRepairMatrixCell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblRepair_matrix_cell', function (Blueprint $table) {
             $table->dropColumn(array('rut_condition', 'crack_condition'));
			 
			 $table->decimal('crack_from', 6, 2);
			 $table->decimal('crack_to', 6, 2);
			 $table->decimal('rut_from', 6, 2);
			 $table->decimal('rut_to', 6, 2);
			 $table->integer('user_id')->nullable();
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
