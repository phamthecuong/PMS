<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLatLngColumnInSectionDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_RMD', function (Blueprint $table) {
            $table->decimal('from_lat', 10, 4)->change();
            $table->decimal('from_lng', 11, 4)->change();
            $table->decimal('to_lat', 10, 4)->change();
            $table->decimal('to_lng', 11, 4)->change();
        });

        Schema::table('tblSectiondata_MH', function (Blueprint $table) {
            $table->decimal('from_lat', 10, 4)->change();
            $table->decimal('from_lng', 11, 4)->change();
            $table->decimal('to_lat', 10, 4)->change();
            $table->decimal('to_lng', 11, 4)->change();
        });

        Schema::table('tblSectiondata_TV', function (Blueprint $table) {
            $table->decimal('lat_station', 10, 4)->change();
            $table->decimal('lng_station', 11, 4)->change();
        });

        Schema::table('tblRMD_history', function (Blueprint $table) {
            $table->decimal('from_lat', 10, 4)->change();
            $table->decimal('from_lng', 11, 4)->change();
            $table->decimal('to_lat', 10, 4)->change();
            $table->decimal('to_lng', 11, 4)->change();
        });

        Schema::table('tblMH_history', function (Blueprint $table) {
            $table->decimal('from_lat', 10, 4)->change();
            $table->decimal('from_lng', 11, 4)->change();
            $table->decimal('to_lat', 10, 4)->change();
            $table->decimal('to_lng', 11, 4)->change();
        });

        Schema::table('tblTV_history', function (Blueprint $table) {
            $table->decimal('lat_station', 10, 4)->change();
            $table->decimal('lng_station', 11, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblSectiondata_RMD', function (Blueprint $table) {
            $table->decimal('from_lat', 10, 8)->change();
            $table->decimal('from_lng', 11, 8)->change();
            $table->decimal('to_lat', 10, 8)->change();
            $table->decimal('to_lng', 11, 8)->change();
        });

        Schema::table('tblSectiondata_MH', function (Blueprint $table) {
            $table->decimal('from_lat', 10, 8)->change();
            $table->decimal('from_lng', 11, 8)->change();
            $table->decimal('to_lat', 10, 8)->change();
            $table->decimal('to_lng', 11, 8)->change();
        });

        Schema::table('tblSectiondata_TV', function (Blueprint $table) {
            $table->decimal('lat_station', 10, 8)->change();
            $table->decimal('lng_station', 11, 8)->change();
        });

        Schema::table('tblRMD_history', function (Blueprint $table) {
            $table->decimal('from_lat', 10, 8)->change();
            $table->decimal('from_lng', 11, 8)->change();
            $table->decimal('to_lat', 10, 8)->change();
            $table->decimal('to_lng', 11, 8)->change();
        });

        Schema::table('tblMH_history', function (Blueprint $table) {
            $table->decimal('from_lat', 10, 8)->change();
            $table->decimal('from_lng', 11, 8)->change();
            $table->decimal('to_lat', 10, 8)->change();
            $table->decimal('to_lng', 11, 8)->change();
        });

        Schema::table('tblTV_history', function (Blueprint $table) {
            $table->decimal('lat_station', 10, 8)->change();
            $table->decimal('lng_station', 11, 8)->change();
        });
    }
}
