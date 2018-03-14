<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPoint', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name',45);
			$table->timestamp('created')->comment('current timestamp');
			$table->decimal('lat',10,8);
			$table->decimal('lng',11,8);
			$table->float('height');
			$table->integer('order_id')->default('1')->comment('1~20');
			$table->string('section_survey_id',25)->comment('format: section_code + survey year');
			$table->integer('oldFlg')->default('0')->comment('0: active data, 1: old data');
			$table->string(' image_path');
			$table->tinyInteger('transferred_flg')->default('0')->comment('1 mean transferred to MASTER server');
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
        Schema::dropIfExists('tblPoint');
    }
}
