<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTblRMDHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('tblRMD_history', function ($table) {
        // // $table->dropColumn('del_flg');
        // // $table->dropForeign('tblsectiondata_tv_sb_id_foreign');
        // $sql = 'ALTER TABLE tblRMD_history
                    // DROP COLUMN effect_at ,
                    // DROP COLUMN nullity_at
                    // ';
        // DB::connection()->getPdo()->exec($sql);
		// $table->dateTime('effect_at')->nullable();
		// $table->dateTime('nullity_at')->nullable();
		// $table->dropColumn('sb_id');
		// $table->dropColumn('branch_id');
		// });
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
