<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblNotification', function(Blueprint $table)
        {
        	$table->increments('id');
        	$table->integer('type')->comment('1.Deterioration, 2.Budget, 3.Dataset');
        	$table->string('reference_id');
        	$table->integer('user_id')->comment('UNSIGNED');
        	$table->integer('status_notification')->comment('0: unread, 1: read')->nullable();
        	$table->integer('status_process')->comment('0: pending, 1: complete')->nullable();
        	$table->integer('percent')->nullable();
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
        //
    }
}
