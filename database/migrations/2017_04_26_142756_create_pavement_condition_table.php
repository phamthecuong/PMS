<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePavementConditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pavement_condition_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section_id');
            $table->string('geographical_area');
            $table->string('rmb');
            $table->string('sb');
            $table->string('route_number');
            $table->string('branch_number');
            $table->string('route_name');
            $table->mediumInteger('kp_from');
            $table->mediumInteger('m_from');
            $table->mediumInteger('kp_to');
            $table->mediumInteger('m_to');
            $table->mediumInteger('section_length');
            $table->float('analysis_area');
            $table->string('structure');
            $table->string('intersection');
            $table->string('overlapping');
            $table->smallInteger('number_of_lane_u');
            $table->smallInteger('number_of_lane_d');
            $table->string('direction', 1);
            $table->string('survey_lane', 2);
            $table->string('surface_type', 4);
            $table->string('survey_year', 4);
            $table->string('survey_month', 2);
            $table->float('cracking');
            $table->float('patching');
            $table->float('pothole');
            $table->float('cracking_ratio');
            $table->float('rutting_max');
            $table->float('rutting_average');
            $table->float('iri');
            $table->float('mci');
            $table->text('note');
            $table->string('process_id', 32);
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
        Schema::dropIfExists('pavement_condition_table');
    }
}
