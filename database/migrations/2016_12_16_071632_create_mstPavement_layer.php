<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstPavementLayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mstPavement_layer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_cd', 10);
            $table->string('name_en', 50);
            $table->string('name_vn', 50);
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('mstPavement_layer')->onDelete('restrict');
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
        Schema::table('mstPavement_layer', function (Blueprint $table) {
            //
        });
    }
}
