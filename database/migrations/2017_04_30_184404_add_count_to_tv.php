<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountToTv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblTV_history', function (Blueprint $table) {
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
        Schema::table('tblTV_history', function (Blueprint $table) {
            $table->dropColumn(['total_traffic_volume_up', 'total_traffic_volume_down', 'heavy_traffic_up', 'heavy_traffic_down']);
        });
    }
}
