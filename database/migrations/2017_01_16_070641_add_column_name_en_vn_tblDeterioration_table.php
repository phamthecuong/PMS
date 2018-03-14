<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNameEnVnTblDeteriorationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblDeterioration', function (Blueprint $table) {
            $table->string('name_en',100)->nullable();
			$table->string('name_vn',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblDeterioration', function (Blueprint $table) {
        	$table->dropColumn('name_en');
			$table->dropColumn('name_vn');
            //
        });
    }
}
