<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTblMHHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblMH_history', function ($table) {
        
		$table->dropColumn('sb_id');
		$table->dropColumn('branch_id');
		$sql = 'ALTER TABLE tblMH_history
                    DROP COLUMN effect_at ,
                    DROP COLUMN nullity_at
                    ';
        DB::connection()->getPdo()->exec($sql);
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
