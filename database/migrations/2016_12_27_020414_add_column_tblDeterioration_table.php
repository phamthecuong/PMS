<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTblDeteriorationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblDeterioration', function ($table) {
			// $table->dropColumn('crack_selected_rank');
			// $table->dropColumn('summary_table_data');
			
			$table->text('crack_summary_table_data')->nullable();
			$table->tinyInteger('crack_selected_rank')->nullable();
			
			
			$table->tinyInteger('status')->default(0)->comment('0: pending, 1: complete. TRIGGER update');
			$table->tinyInteger('benchmark_flg')->default(0)->comment('0: pending, 3: complete');
			
			
			
			$table->text('rut_summary_table_data')->nullable();
			$table->tinyInteger('rut_selected_rank')->nullable();
			
			$table->text('iri_summary_table_data')->nullable();
			$table->tinyInteger('iri_selected_rank')->nullable();
			
			$table->text('log_text')->nullable();
			$table->tinyInteger('pav_type_flg')->default(0)->comment('0: pending, 6: complete');
			$table->tinyInteger('route_flg')->default(0)->comment('0: pending, 6: complete');
			$table->tinyInteger('section_flg')->default(0)->comment('0: pending, 6: complete');
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
