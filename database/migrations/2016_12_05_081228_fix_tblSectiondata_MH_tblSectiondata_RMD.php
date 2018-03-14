<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTblSectiondataMHTblSectiondataRMD extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_RMD', function ($table) {
            $table->dropColumn('sectiondata_id');
        });

        Schema::table('tblSectiondata_MH', function ($table) {
            $table->dropColumn('sectiondata_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
