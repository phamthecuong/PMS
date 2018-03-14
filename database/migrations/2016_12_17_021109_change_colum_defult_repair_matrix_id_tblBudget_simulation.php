<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumDefultRepairMatrixIdTblBudgetSimulation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblBudget_simulation', function (Blueprint $table) {
            $table->integer('defult_repair_matrix_id')->nullable()->default(NULL)->comment('NULL: Default Repair Matrix')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblBudget_simulation', function (Blueprint $table) {
            //
        });
    }
}
