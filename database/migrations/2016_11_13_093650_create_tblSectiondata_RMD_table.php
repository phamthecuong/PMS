<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSectiondataRMDTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSectiondata_RMD', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sectiondata_id');
            $table->integer('segment_id');
            $table->integer('terrian_type_id');
            $table->integer('road_class_id');
            $table->decimal('from_lat', 10, 8)->nullable();
            $table->decimal('from_lng', 11, 8)->nullable();
            $table->decimal('to_lat', 10, 8)->nullable();
            $table->decimal('to_lng', 11, 8)->nullable();
            $table->integer('km_from');
            $table->integer('m_from');
            $table->integer('km_to');
            $table->integer('m_to');
            $table->date('survey_time');
            $table->tinyInteger('direction')->comment('-1:left, 1:right, 0:single');
            $table->smallInteger('lane_pos_number');
            $table->float('lane_width', 8, 2);
            $table->integer('no_lane');
            $table->string('construct_year');
            $table->string('service_start_year');
            $table->float('temperature', 8, 2);
            $table->float('annual_precipitation', 8, 2);
            $table->float('actual_length', 8, 2);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->tinyInteger('del_flg');
            $table->integer('sb_id');
            $table->integer('branch_id');
            $table->string('remark');
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
        Schema::dropIfExists('tblSectiondata_RMD');
    }
}
