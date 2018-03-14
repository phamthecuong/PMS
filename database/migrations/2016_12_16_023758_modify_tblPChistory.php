<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTblPChistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSection_PC_history', function (Blueprint $table) {
            $table->dropColumn('tblSection_PC_id');
            $table->renameColumn('KP_from', 'km_from');
            $table->renameColumn('M_to', 'm_to');
            $table->renameColumn('KP_to', 'km_to');
            $table->renameColumn('M_from', 'm_from');
            $table->string('structure', 50)->nullable()->change();
            $table->smallInteger('section_length')->unsigned()->change();
            $table->smallInteger('number_of_lane_U')->tinyInteger('number_of_lane_U')->unsigned()->change();
            $table->smallInteger('number_of_lane_D')->tinyInteger('number_of_lane_D')->unsigned()->change();
            $table->renameColumn('survey_lane_UD', 'direction')->string('direction', 1)->comment('U|D')->nullable(false)->change();
            $table->renameColumn('survey_lane_path_lane', 'lane_position_no')->smallInteger('lane_position_no')->tinyInteger('lane_position_no')->nullable(false)->unsigned()->change();
            $table->string('surface_type', 10)->change();
            $table->renameColumn('survey_year', 'date_y');
            $table->renameColumn('survey_month', 'date_m');
            $table->renameColumn('crackingRatio_cracking', 'cracking_ratio_cracking')->decimal('cracking_ratio_cracking', 8, 4)->change();
            $table->renameColumn('crackingRatio_patching', 'cracking_ratio_patching')->decimal('cracking_ratio_patching', 8, 4)->change();
            $table->renameColumn('crackingRatig_pothole', 'cracking_ratio_pothole')->decimal('cracking_ratio_pothole', 8, 4)->change();
            $table->renameColumn('crackingRatio_total', 'cracking_ratio_total')->decimal('cracking_ratio_total', 8, 4)->change();
            $table->renameColumn('ruttingDepth_max', 'rutting_depth_max')->decimal('rutting_depth_max', 8, 4)->change();
            $table->renameColumn('ruttingDepth_ave', 'rutting_depth_ave')->decimal('rutting_depth_ave', 8, 4)->change();
            $table->text('note')->nullable()->change();            
            $table->decimal('analysis_area', 10, 4)->nullable()->change();
            $table->string('structure', 50)->nullable()->change();
            $table->string('status', 10)->comment('insert, update, delete')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblPC_history', function (Blueprint $table) {
            //
        });
    }
}
