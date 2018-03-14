<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTblSectiondataMH extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table('tblSectiondata_MH', function ($table) {
        // $table->dropColumn('del_flg');
         $table->dropForeign('tblsectiondata_mh_sb_id_foreign');
		$table->dropColumn('sb_id');
		$table->dropColumn('branch_id');
		$table->dateTime('effect_at')->nullable();
		$table->dateTime('nullity_at')->nullable();
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
