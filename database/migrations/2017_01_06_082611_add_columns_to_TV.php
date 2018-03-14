<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToTV extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_TV', function (Blueprint $table) {
            $table->unsignedInteger('total_traffic_volume_up');
            $table->unsignedInteger('total_traffic_volume_down');
            $table->unsignedInteger('heavy_traffic_up');
            $table->unsignedInteger('heavy_traffic_down');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblSectiondata_TV', function (Blueprint $table) {
            //
        });
    }
}
