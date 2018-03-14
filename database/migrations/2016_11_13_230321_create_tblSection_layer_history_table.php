<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSectionLayerHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSection_layer_history', function (Blueprint $table) {
            $table->increments('id');
            $table->float('thickness', 8, 2);
            $table->string('description');
            $table->integer('material_type_id');
            $table->tinyInteger('section_layer_id');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->enum('status',['INSERT','UPDATE','DELETE']);
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
        Schema::dropIfExists('tblSection_layer_history');
    }
}
