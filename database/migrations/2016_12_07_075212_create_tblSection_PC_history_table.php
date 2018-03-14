<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSectionPCHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSection_PC_history', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('tblSection_PC_id');

            $table->string('section_code',25);
            $table->string('geographical_area',50);
            $table->string('road_category')->nullable();
            $table->integer('SB_id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->integer('KP_from')->unsigned();
            $table->integer('M_to');
            $table->integer('KP_to')->unsigned();
            $table->integer('M_from');
            $table->decimal('section_length',12,4);
            $table->decimal('analysis_area',12,4);
            $table->string('structure',50);
            $table->string('intersection',45)->nullable();
            $table->string('overlapping',45)->nullable();
            $table->float('number_of_lane_U',12,4)->nullable();
            $table->float('number_of_lane_D',12,4)->nullable();
            $table->integer('survey_lane_UD')->nullable();
            $table->integer('survey_lane_path_lane')->nullable();
            $table->string('surface_type',45)->nullable();
            $table->string('survey_year',4);
            $table->string('survey_month',2);
            $table->float('crackingRatio_cracking',12,4);
            $table->float('crackingRatio_patching',12,4);
            $table->float('crackingRatig_pothole',12,4);
            $table->float('crackingRatio_total',12,4);
            $table->float('ruttingDepth_max',12,4);
            $table->float('ruttingDepth_ave',12,4);
            $table->float('IRI');
            $table->float('MCI');
            $table->text('note');

            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamp('effect_at')->nullable();
            $table->timestamp('nullity_at')->nullable();
            
            $table->string('status')->comment('insert , update , delete');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblSection_PC_history');
    }
}
