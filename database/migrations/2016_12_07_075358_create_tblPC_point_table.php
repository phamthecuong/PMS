<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPCPointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPC_point', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50);
            $table->decimal('lat',10,8);
            $table->decimal('lng',11,8);
            $table->double('height',8,2);
            $table->integer('order_id')->default(1)->comment('1~20');
            // $table->string('section_id',25);
            $table->integer('section_id')->unsigned();
            // $table->integer('oldFlg')->default(0)->comment('0: active data, 1: old data ');
            $table->string('image_path');
            $table->tinyInteger('transferred_flg')->default(0)->comment('1 mean transferred to MASTER server ');
            
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamp('effect_at')->nullable();
            $table->timestamp('nullity_at')->nullable();
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
        Schema::dropIfExists('tblPC_point');
    }
}
