<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPCCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPC_credential', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url_pavement_conditions');
            $table->string('url_image_table');
            $table->integer('user_id');
            $table->datetime('created_time')->comment('current timestamp');
            $table->integer('hidden')->default('0')->comment('1: hidden, 0:show');
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
        Schema::dropIfExists('tblPC_credential');
    }
}
