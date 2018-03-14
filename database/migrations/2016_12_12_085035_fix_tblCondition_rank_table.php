<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTblConditionRankTable extends Migration
{
    
    public function up()
    {
        Schema::table('tblCondition_rank', function ($table) {
            $table->integer('from');
			$table->integer('to')->nullable();
			$table->dropColumn('value');
        });
    }

    
    public function down()
    {
        //
    }
}
