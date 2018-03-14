<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumDeteriorationIdTblBudgetSimulation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblBudget_simulation', function (Blueprint $table) {
            $table->string('deterioration_id', 36);
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
