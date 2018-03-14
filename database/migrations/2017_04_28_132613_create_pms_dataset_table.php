<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePmsDatasetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPMS_dataset', function (Blueprint $table) {
            $table->increments('id');
            $table->string('year', 4);
            $table->integer('total_segment')->default(0)->unsigned();
            $table->integer('completed_segment')->default(0)->unsigned();
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
        Schema::dropIfExists('tblPMS_dataset');
    }
}
