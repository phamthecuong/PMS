<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblBudgetSimulationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblBudget_simulation', function (Blueprint $table) {
            $table->uuid('id');
			$table->primary('id');
			$table->unsignedInteger('budget_constraint');
			$table->decimal('target_risk', 5, 2)->unsigned();
			$table->string('year', 4);
			$table->integer('output_0_flg')->default(0)->comment('0 pending, 1: complete');
			$table->integer('output_1_flg')->default(0)->comment('0 pending, 1: complete');
			$table->integer('output_2_flg')->default(0)->comment('0 pending, 1: complete');
			$table->integer('output_3_flg')->default(0)->comment('0 pending, 1: complete');
			$table->integer('status')->default(0)->comment('Status of session: 0 pending, 1: complete');
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
        Schema::dropIfExists('tbl_budget_simulations');
    }
}
