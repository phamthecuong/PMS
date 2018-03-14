<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Foreignkey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblOrganization', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned()->nullable()->default(null)->change();
            $table->foreign('parent_id')->references('id')->on('tblOrganization')->onDelete('restrict');
        });
        
        Schema::table('tblSegment', function (Blueprint $table) {
            $table->integer('SB_id')->unsigned()->change();
            $table->foreign('SB_id')->references('id')->on('tblOrganization')->onDelete('restrict');
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
