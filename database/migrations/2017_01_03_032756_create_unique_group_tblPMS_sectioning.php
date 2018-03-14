<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniqueGroupTblPMSSectioning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblPMS_sectioning', function (Blueprint $table) {
            $table->unique(array('km_from', 'm_from', 'km_to', 'm_to', 'direction', 'branch_id', 'lane_pos_no'), 'pms_sectioning');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblPMS_sectioning', function (Blueprint $table) {
            //
        });
    }
}
