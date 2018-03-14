<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblConditionRankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblCondition_rank', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('target_type')->comment('1: Crack ratio, 2: rutting depth, 3: IRI');
            $table->string('value')->comment('e.g: =,2  or  >=,2,<=,3');
            $table->tinyInteger('rank')->default(1);
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('tblCondition_rank');
    }
}
