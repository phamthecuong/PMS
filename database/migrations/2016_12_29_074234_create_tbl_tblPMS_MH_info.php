<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTblPMSMHInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPMS_MH_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('PMS_info_id');
            $table->string('completion_year', 4);
            $table->string('completion_month', 2);
            $table->integer('r_category_id');
            $table->integer('r_classification_id');
            $table->integer('r_structType_id');
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
        Schema::dropIfExists('tblPMS_MH_info');
    }
}
