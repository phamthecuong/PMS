<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTblPMSPCInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPMS_PC_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('PMS_info_id');
            $table->string('structure', 50)->nullable();
            $table->string('intersection', 45)->nullable();
            $table->string('geographical_area', 45)->nullable();
            $table->decimal('analysis_area', 10, 4)->nullable();
            $table->unsignedTinyInteger('number_of_lane')->nullable();
            $table->integer('pavement_type_code')->nullable();
            $table->string('pavement_type', 50)->nullable();
            $table->decimal('cracking', 8, 4);
            $table->decimal('patching', 8, 4);
            $table->decimal('pothole', 8, 4);
            $table->decimal('cracking_ratio', 8, 4);
            $table->decimal('rutting_max', 8, 4);
            $table->decimal('rutting_ave', 8, 4);
            $table->decimal('IRI', 8, 4);
            $table->decimal('MCI', 8, 4);
            $table->unsignedSmallInteger('section_length');
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
        Schema::dropIfExists('tblPMS_PC_info');
    }
}
