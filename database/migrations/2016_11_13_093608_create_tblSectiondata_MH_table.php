<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSectiondataMHTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSectiondata_MH', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sectiondata_id');
            $table->integer('segment_id')->nullable();
            $table->integer('km_from');
            $table->integer('m_from');
            $table->integer('km_to');
            $table->integer('m_to');
            $table->decimal('from_lat', 10, 8)->nullable();
            $table->decimal('from_lng', 11, 8)->nullable();
            $table->decimal('to_lat', 10, 8)->nullable();
            $table->decimal('to_lng', 11, 8)->nullable();
            $table->date('survey_time');
            $table->date('completion_date')->comment('A date (year and month) of repair work completion. It can be specified by YY/MM/DD as well.');
            $table->integer('repair_duration')->comment('A total time required for repair work completion.');
            $table->tinyInteger('direction')->comment('0:left, 1:right, 2:single');
            $table->float('actual_length',8, 2);
            $table->tinyInteger('lane_pos_number')->comment('Total Width of Repair Lane');
            $table->float('total_width_repair_lane', 8, 2);
            $table->integer('r_classification_id');
            $table->integer('r_structType_id');
            $table->integer('r_category_id');
            $table->integer('created_user');
            $table->integer('updated_user');
            $table->tinyInteger('del_flg');
            $table->integer('sb_id');
            $table->integer('branch_id');
            $table->float('distance', 8, 2);
            $table->integer('direction_running');
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
        Schema::dropIfExists('tblSectiondata_MH');
    }
}
