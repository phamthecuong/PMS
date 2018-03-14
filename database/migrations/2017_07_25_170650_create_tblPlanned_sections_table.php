<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPlannedSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPlanned_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section_id', 25)->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('sb_id')->nullable();
            $table->integer('km_from')->nullable();
            $table->integer('m_from')->nullable();
            $table->integer('km_to')->nullable();
            $table->integer('m_to')->nullable();
            $table->unsignedSmallInteger('section_length')->nullable();
            $table->tinyInteger('direction')->nullable()->comment('1: left, 2: right, 3: single');
            $table->unsignedTinyInteger('lane_pos_no')->nullable();
            $table->string('planned_year', 4)->nullable();
            $table->unsignedInteger('repair_quantity')->nullable();
            $table->unsignedInteger('repair_cost')->nullable();
            $table->string('repair_method_en', 45)->nullable();
            $table->string('repair_method_vn', 45)->nullable();
            $table->string('repair_classification_en', 45)->nullable();
            $table->string('repair_classification_vn', 45)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::drop('tblPlanned_sections');
    }
}
