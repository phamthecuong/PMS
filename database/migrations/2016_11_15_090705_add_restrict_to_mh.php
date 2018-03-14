<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRestrictToMh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_MH', function (Blueprint $table) {
            $table->integer('sb_id')->unsigned()->change();
            $table->integer('created_user')->nullable()->change();
            $table->integer('updated_user')->nullable()->change();
            $table->renameColumn('created_user', 'created_by');
            $table->renameColumn('updated_user', 'updated_by');
            $table->foreign('sb_id')->references('id')->on('tblOrganization')->onDelete('restrict');
        });

        Schema::table('tblMH_history', function (Blueprint $table) {
            $table->dropColumn(array('created_by', 'updated_by'));
        });

        Schema::table('tblMH_history', function (Blueprint $table) {
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
        Schema::table('tblSectiondata_MH', function (Blueprint $table) {
            //
        });
    }
}
