<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMergeSplitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblMerge_split', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('1:Merge 2:Split');
            $table->tinyInteger('action')->comment('1:Segment , 2:Organization ');
            $table->tinyInteger('object');
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
        Schema::dropIfExists('tblMerge_split');
    }
}
