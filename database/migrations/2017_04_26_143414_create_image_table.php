<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section_id');
            $table->smallInteger('image_id');
            $table->smallInteger('route_number');
            $table->smallInteger('branch_number');
            $table->string('direction', 1);
            $table->tinyInteger('survey_lane');
            $table->decimal('longitude', 11, 8);
            $table->decimal('latitude', 10, 8);
            $table->float('height');
            $table->text('image_path');
            $table->string('process_id', 32);
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
        Schema::dropIfExists('image_table');
    }
}
