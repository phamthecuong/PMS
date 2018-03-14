<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignkeyToOrganization extends Migration
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
            $table->integer('created_by')->nullable()->change();
            $table->integer('updated_by')->nullable()->change();
            $table->foreign('parent_id')->references('id')->on('tblOrganization')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblOrganization', function (Blueprint $table) {
            //
        });
    }
}