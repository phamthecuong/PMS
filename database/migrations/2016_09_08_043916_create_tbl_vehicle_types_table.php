<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVehicleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblVehicle_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en',45);
            $table->string('name_vn',45);
            $table->tinyInteger('type')->default('0')->comment('0: Heavy traffic volume and 1 : traffic volume');
            $table->string('description',45);
            $table->tinyInteger('active_flg')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblVehicle_type');
    }
}
