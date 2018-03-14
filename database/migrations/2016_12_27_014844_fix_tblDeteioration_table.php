<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTblDeteiorationTable extends Migration
{
    
    public function up()
    {
        Schema::table('tblDeterioration', function ($table) {
            $table->dropColumn('region_id');
            $table->dropColumn('distress_type');
			$table->dropColumn('selected_rank');
			$table->dropColumn('pav_type_fi_flg');
			$table->dropColumn('pav_type_eps_flg');
			$table->dropColumn('route_21_flg');
			$table->dropColumn('route_22_flg');
			$table->dropColumn('section_31_flg');
			$table->dropColumn('section_32_flg');
			
			$table->dropColumn('status');
			$table->dropColumn('benchmark_flg');

			$table->dropColumn('summary_table_data');
        });
    }

    public function down()
    {
        
    }
}
