<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTblPMSSectioningInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPMS_sectioning_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('PMS_section_id');
            $table->unsignedTinyInteger('type_id');
            $table->string('condition_year', 4);
            $table->string('condition_month', 2);
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
        Schema::dropIfExists('tblPMS_sectioning_info');
    }
}
