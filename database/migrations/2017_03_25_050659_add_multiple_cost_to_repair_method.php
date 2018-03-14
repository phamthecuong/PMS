<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMultipleCostToRepairMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblRepair_method_cost', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('repair_method_id')->unsigned();
            $table->integer('organization_id')->unsigned();
            $table->integer('cost')->unsigned();
            $table->char("created_by", 32)->nullable();
            $table->char("updated_by", 32)->nullable();
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
        Schema::dropIfExists('tblRepair_method_cost');
    }
}
