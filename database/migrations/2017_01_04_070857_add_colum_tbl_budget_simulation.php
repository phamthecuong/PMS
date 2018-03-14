<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumTblBudgetSimulation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblBudget_simulation', function (Blueprint $table) {
            $table->integer('simulation_term')->nullable();
			$table->integer('simulation_time')->nullable();
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
