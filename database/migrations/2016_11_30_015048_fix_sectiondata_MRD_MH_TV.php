<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixSectiondataMRDMHTV extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_RMD', function ($table) {
            $table->integer('created_by')->nullable()->change();
            $table->integer('updated_by')->nullable()->change();
        });
        Schema::table('tblSectiondata_MH', function ($table) {
            $table->integer('created_by')->nullable()->change();
            $table->integer('updated_by')->nullable()->change();
        });
        Schema::table('tblSectiondata_TV', function ($table) {
            $table->integer('created_by')->nullable()->change();
            $table->integer('updated_by')->nullable()->change();
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
