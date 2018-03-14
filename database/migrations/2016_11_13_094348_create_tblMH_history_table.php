<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMHHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblMH_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sectiondata_id');
            $table->integer('segment_id');
            $table->integer('km_from');
            $table->integer('m_from');
            $table->integer('km_to');
            $table->integer('m_to');
            $table->decimal('from_lat', 10, 8);
            $table->decimal('from_lng', 11, 8);
            $table->decimal('to_lat', 10, 8);
            $table->decimal('to_lng', 11, 8);
            $table->date('survey_time');
            $table->date('completion_date')->comment('A date (year and month) of repair work completion. It can be specified by YY/MM/DD as well.');
            $table->string('repair_duration',50);
            $table->tinyInteger('direction')->comment('-1:left, 1:right, 0:single');
            $table->float('actual_length',8, 2);
            $table->smallInteger('lane_pos_number');
            $table->float('total_width_repair_lane', 8, 2)->comment('Total Width of Repair Lane');
            $table->integer('r_classification_id');
            $table->integer('r_structType_id');
            $table->integer('r_category_id');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->tinyInteger('del_flg');
            $table->integer('sb_id');
            $table->integer('branch_id');
            $table->float('distance', 8, 2);
            $table->tinyInteger('direction_running');
            $table->string('remark');
            $table->enum('status', ['insert', 'delete','update']);
            $table->datetime('effect_at');
            $table->datetime('nullity_at');
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
        Schema::dropIfExists('tblMH_history');
    }
}
