<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullableBudgetConstraintAndTargetRiskTblBudgetSimulation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblBudget_simulation', function (Blueprint $table) {
            $table->unsignedInteger('budget_constraint')->nullable()->change();
			$table->decimal('target_risk', 5, 2)->unsigned()->nullable()->change();
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
