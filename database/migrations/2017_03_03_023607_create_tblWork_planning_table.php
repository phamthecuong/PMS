<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWorkPlanningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblWork_planning', function (Blueprint $table) {
            $table->string('cd_id', 36)->comment('UNIQUE');
            $table->integer('default_repair_matrix_id');
            $table->string('year', 4);
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
        });

        Schema::table('tblWork_planning', function ($table) {
            $table->dropColumn('cd_id');
            $table->string('id', 36);
        });

        Schema::table('tblWork_planning', function ($table) {
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblWork_planning');
    }
}
