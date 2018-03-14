<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRestrictToTv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_TV', function (Blueprint $table) {
            $table->integer('sb_id')->unsigned()->change();
            $table->integer('created_by')->nullable()->change();
            $table->integer('updated_by')->nullable()->change();
            $table->foreign('sb_id')->references('id')->on('tblOrganization')->onDelete('restrict');
        });

        Schema::table('tblTV_history', function (Blueprint $table) {
            $table->dropColumn(array('created_by', 'updated_by'));
        });

        Schema::table('tblTV_history', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
