<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblMH_history', function (Blueprint $table) {
            $table->integer('pavement_type_id')->nullable();
            $table->integer('ward_from_id')->nullable();
            $table->integer('ward_to_id')->nullable();
        });

        Schema::table('tblRMD_history', function (Blueprint $table) {
            $table->integer('ward_from_id')->nullable();
            $table->integer('ward_to_id')->nullable();
        });

        Schema::table('tblTV_history', function (Blueprint $table) {
            $table->integer('ward_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblMH_history', function (Blueprint $table) {
            $table->dropColumn('pavement_type_id');
            $table->dropColumn('ward_from_id');
            $table->dropColumn('ward_to_id');
        });

        Schema::table('tblRMD_history', function (Blueprint $table) {
            $table->dropColumn('ward_from_id');
            $table->dropColumn('ward_to_id');
        });

        Schema::table('tblTV_history', function (Blueprint $table) {
            $table->dropColumn('ward_id');
        });
    }
}
