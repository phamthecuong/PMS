<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixColumnNameTblOrganization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblOrganization', function (Blueprint $table) {
            $table->renameColumn('headquater_en', 'headquarter_en');
            $table->renameColumn('headquater_vn', 'headquarter_vn');
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
