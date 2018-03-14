<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnForTblWorkPlanningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblWork_planning', function (Blueprint $table) {
            $table->renameColumn('excel_flg', 'excel_flg_0');
            $table->integer('excel_flg_1')->nullable();
            $table->integer('excel_flg_2')->nullable();
            $table->boolean('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblWork_planning', function (Blueprint $table) {
            $table->dropColumn('excel_flg_1');
            $table->dropColumn('excel_flg_2');
            $table->dropColumn('status');
        });
    }
}
