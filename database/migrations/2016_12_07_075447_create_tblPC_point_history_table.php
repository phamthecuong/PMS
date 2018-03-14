<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPCPointHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPC_point_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('PC_point_id');

            $table->string('name',50);
            $table->decimal('lat',10,8);
            $table->decimal('lng',11,8);
            $table->double('height',8,2);
            $table->integer('order_id')->default(1)->comment('1~20');
            $table->integer('section_id');
            $table->string('image_path');
            $table->tinyInteger('transferred_flg')->default(0)->comment('1 mean transferred to MASTER server ');
            
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamp('effect_at')->nullable();
            $table->timestamp('nullity_at')->nullable();
            $table->string('status')->comment('insert , update , delete');
            
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
        Schema::dropIfExists('tblPC_point_history');
    }
}
