<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInTblSectiondataMhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_MH', function (Blueprint $table) {
            $table->integer('ward_from_id')->nullable();
            $table->integer('ward_to_id')->nullable();
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
            $table->dropColumn('ward_from_id');
            $table->dropColumn('ward_to_id');
        });
    }
}
