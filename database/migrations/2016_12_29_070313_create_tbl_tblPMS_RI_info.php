<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTblPMSRIInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPMS_RI_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('PMS_info_id');
            $table->unsignedInteger('segment_id');
            $table->string('service_start_year', 4)->nullable();
            $table->string('service_start_month', 2)->nullable();
            $table->string('construct_year', 4)->nullable();
            $table->string('construct_month', 2)->nullable();
            $table->integer('pavement_type_code')->nullable();
            $table->string('pavement_type', 50)->nullable();
            $table->float('pavement_thickness')->nullable();
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
        Schema::dropIfExists('tblPMS_RI_info');
    }
}
