<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTblPMSSectioning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPMS_sectioning', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('km_from');
            $table->integer('m_from');
            $table->integer('km_to');
            $table->integer('m_to');
            $table->tinyInteger('direction')->comment('-1: left, 0: single, 1: right');
            $table->unsignedInteger('branch_id');
            $table->unsignedTinyInteger('lane_pos_no');
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
        Schema::dropIfExists('tblPMS_sectioning');
    }
}
