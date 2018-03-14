<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstLayerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mstLayer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_cd',45);
            $table->string('name_en',45);
            $table->string('name_vn',45);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('active_flg');
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
        Schema::dropIfExists('mstLayer');
    }
}
