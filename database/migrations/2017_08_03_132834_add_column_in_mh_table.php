<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInMhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_MH', function (Blueprint $table) {
            $table->integer('repair_method_id')->nullable();
        });

        Schema::table('tblMH_history', function (Blueprint $table) {
            $table->integer('repair_method_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblSectiondata_MH', function (Blueprint $table) {
            $table->dropColumn('repair_method_id');
        });

        Schema::table('tblMH_history', function (Blueprint $table) {
            $table->dropColumn('repair_method_id');
        });
    }
}
