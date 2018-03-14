<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTblSectionPC extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSection_PC', function (Blueprint $table) {
            $table->renameColumn('section_id', 'section_code');
            $table->dropColumn('jurisdiction');
            $table->dropColumn('management_agency');
            $table->dropColumn('route_name');
            $table->renameColumn('KP_from', 'km_from');
            $table->renameColumn('M_to', 'm_to');
            $table->renameColumn('KP_to', 'km_to');
            $table->renameColumn('M_from', 'm_from');
            $table->smallInteger('section_length')->unsigned()->change();
            $table->smallInteger('number_of_lane_U')->tinyInteger('number_of_lane_U')->unsigned()->change();
            $table->smallInteger('number_of_lane_D')->tinyInteger('number_of_lane_D')->unsigned()->change();
            $table->renameColumn('survey_lane_UD', 'direction')->string('direction', 1)->comment('U|D')->nullable(false)->change();
            $table->renameColumn('survey_lane_path_lane', 'lane_position_no')->smallInteger('lane_position_no')->tinyInteger('lane_position_no')->nullable(false)->unsigned()->change();
            $table->string('surface_type', 10)->change();
            $table->renameColumn('crackingRatio_cracking', 'cracking_ratio_cracking');
            $table->renameColumn('crackingRatio_patching', 'cracking_ratio_patching');
            $table->renameColumn('crackingRatig_pothole', 'cracking_ratio_pothole');
            $table->renameColumn('crackingRatio_total', 'cracking_ratio_total');
            $table->renameColumn('ruttingDepth_max', 'rutting_depth_max');
            $table->renameColumn('ruttingDepth_ave', 'rutting_depth_ave');
            $table->renameColumn('Note', 'note')->text('note')->nullable()->change();
            $table->integer('SB_id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->decimal('analysis_area', 10, 4)->nullable()->change();
            $table->string('structure', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblSection_PC', function (Blueprint $table) {
            //
        });
    }
}
