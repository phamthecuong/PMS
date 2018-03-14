<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeParameterId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblRepair_matrix_cell_values', function (Blueprint $table) {
            $table->string('parameter_id', 20)->comment('link to code')->change();
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
