<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblBranch', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('route_id');
            $table->string('name_en',45);
            $table->string('name_vn',45);
            $table->string('org_start_chainage',45);
            $table->string('org_end_chainage',45);
            $table->tinyInteger('order_inbranch_id'); 
            $table->integer('start_side');
            $table->string('description_en',45);
            $table->string('description_vn',45);
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('tblBranch');
    }
}
