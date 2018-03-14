<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mstSetting', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('created_time')->comment('current timestamp')->nullable();
            $table->datetime('updated_time')->nullable();
            $table->string('code',4);
            $table->string('value',5);
            $table->text('description');
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
        Schema::dropIfExists('mstSetting');
    }
}
