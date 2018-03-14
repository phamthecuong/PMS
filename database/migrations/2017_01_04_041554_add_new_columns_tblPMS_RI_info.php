<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsTblPMSRIInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblPMS_RI_info', function (Blueprint $table) {
            $table->float('lane_width')->nullable();
            $table->float('annual_precipitation')->nullable();
            $table->float('temperature')->nullable();
            $table->integer('terrain_type_id')->nullable();
            $table->integer('road_class_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblPMS_RI_info', function (Blueprint $table) {
            //
        });
    }
}
