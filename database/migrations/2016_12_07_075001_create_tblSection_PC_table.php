<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSectionPCTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSection_PC', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section_id',25); //section_survey_id section_data
            $table->string('geographical_area',50);
            $table->string('road_category')->nullable();
            $table->string('jurisdiction');
            $table->string('management_agency');
            $table->string('route_name');
            $table->integer('KP_from');
            $table->integer('M_to');
            $table->integer('KP_to');
            $table->integer('M_from');

            $table->string('section_length');
            $table->float('analysis_area',8,4);
            $table->string('structure',50);

            $table->string('intersection',45)->nullable();
            $table->string('overlapping',45)->nullable();
            $table->float('number_of_lane_U',8,4)->nullable();
            $table->float('number_of_lane_D',8,4)->nullable();
            $table->integer('survey_lane_UD')->nullable();

            $table->integer('survey_lane_path_lane')->nullable();
            $table->string('surface_type',45)->nullable();
            $table->string('date_y',4);
            $table->string('date_m',2);

            $table->float('crackingRatio_cracking',8,4);
            $table->float('crackingRatio_patching',8,4);
            $table->float('crackingRatig_pothole',8,4);
            $table->float('crackingRatio_total',8,4);
            $table->float('ruttingDepth_max',8,4);
            $table->float('ruttingDepth_ave',8,4);

            $table->float('IRI');
            $table->float('MCI');
            $table->text('Note');

            $table->timestamp('effect_at')->nullable();
            $table->timestamp('nullity_at')->nullable();
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
        Schema::dropIfExists('tblSection_PC');
    }
}
