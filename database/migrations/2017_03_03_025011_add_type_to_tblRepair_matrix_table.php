<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToTblRepairMatrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblRepair_matrix', function (Blueprint $table) {
            $table->tinyInteger('type')->comment('1.budget 2.work planning')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblRepair_matrix', function (Blueprint $table) {
            //
        });
    }
}
