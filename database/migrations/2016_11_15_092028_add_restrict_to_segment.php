<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRestrictToSegment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSegment', function (Blueprint $table) {
            $table->integer('SB_id')->unsigned()->change();
            $table->integer('created_by')->nullable()->change();
            $table->integer('updated_by')->nullable()->change();
            $table->foreign('SB_id')->references('id')->on('tblOrganization')->onDelete('restrict');
        });

        Schema::table('tblSegment_history', function (Blueprint $table) {
            $table->dropColumn(array('created_by', 'updated_by'));
        });

        Schema::table('tblSegment_history', function (Blueprint $table) {
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
