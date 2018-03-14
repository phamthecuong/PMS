<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTblPMSTVInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPMS_TV_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('PMS_info_id');
            $table->unsignedInteger('total_traffic_volume');
            $table->unsignedInteger('heavy_traffic');
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
        Schema::dropIfExists('tblPMS_TV_info');
    }
}
