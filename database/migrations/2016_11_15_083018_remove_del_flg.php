<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDelFlg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_RMD', function (Blueprint $table) {
            $table->dropColumn('del_flg');
        });
        Schema::table('tblRMD_history', function (Blueprint $table) {
            $table->dropColumn('del_flg');
        });
        Schema::table('tblSectiondata_MH', function (Blueprint $table) {
            $table->dropColumn('del_flg');
        });
        Schema::table('tblMH_history', function (Blueprint $table) {
            $table->dropColumn('del_flg');
        });
        Schema::table('tblSectiondata_TV', function (Blueprint $table) {
            $table->dropColumn('del_flg');
        });
        Schema::table('tblTV_history', function (Blueprint $table) {
            $table->dropColumn('del_flg');
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
